#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>
#include <time.h>

// Wi-Fi credentials
const char* WIFI_SSID = "YOUR_WIFI_NAME";
const char* WIFI_PASSWORD = "YOUR_WIFI_PASSWORD";

// Laravel API address
const char* SERVER_URL =
  "http://192.168.254.102:8000/api/iot/readings";

// Use the same token stored in your Laravel .env file
const char* DEVICE_TOKEN =
  "YOUR_IOT_DEVICE_TOKEN";

// DHT11 configuration
#define DHTPIN 4
#define DHTTYPE DHT11

DHT dht(DHTPIN, DHTTYPE);

// Device information
const char* DEVICE_ID = "ESP32-GREENHOUSE-01";

unsigned long previousMillis = 0;
const unsigned long interval = 30000;

void connectWiFi() {
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  Serial.print("Connecting to Wi-Fi");

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println();
  Serial.println("Wi-Fi connected!");

  Serial.print("ESP32 IP: ");
  Serial.println(WiFi.localIP());
}

void sendReading() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("Wi-Fi disconnected.");
    return;
  }

  float temperature = dht.readTemperature();
  float humidity = dht.readHumidity();

  if (isnan(temperature) || isnan(humidity)) {
    Serial.println("Failed to read DHT11 sensor.");
    return;
  }

  struct tm timeinfo;
  char timestamp[25];

  if (!getLocalTime(&timeinfo, 2000)) {
    Serial.println("Time not synchronized; reading skipped.");
    return;
  }

  strftime(timestamp, sizeof(timestamp),
           "%Y-%m-%dT%H:%M:%SZ", &timeinfo);

  String payload = "{";
  payload += "\"device_id\":\"";
  payload += DEVICE_ID;
  payload += "\",\"temperature\":";
  payload += String(temperature, 2);
  payload += ",\"humidity\":";
  payload += String(humidity, 2);
  payload += ",\"recorded_at\":\"";
  payload += timestamp;
  payload += "\"}";

  HTTPClient http;
  http.setTimeout(5000);
  http.begin(SERVER_URL);

  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");

  String authorization = "Bearer ";
  authorization += DEVICE_TOKEN;
  http.addHeader("Authorization", authorization);

  int httpCode = http.POST(payload);

  Serial.println("-------------------------");
  Serial.print("Temperature: ");
  Serial.println(temperature);

  Serial.print("Humidity: ");
  Serial.println(humidity);

  Serial.print("HTTP status: ");
  Serial.println(httpCode);

  if (httpCode > 0) {
    Serial.println(http.getString());
  } else {
    Serial.println(http.errorToString(httpCode));
  }

  http.end();
}

void setup() {
  Serial.begin(115200);

  dht.begin();

  Serial.println("Smart Greenhouse Monitoring System");

  connectWiFi();

  // Synchronize the clock using NTP
  configTime(0, 0, "pool.ntp.org", "time.google.com");

  struct tm timeinfo;

  if (!getLocalTime(&timeinfo, 15000)) {
    Serial.println("NTP synchronization failed.");
  }

  sendReading();
  previousMillis = millis();
}

void loop() {
  if (millis() - previousMillis >= interval) {
    previousMillis = millis();
    sendReading();
  }
}
