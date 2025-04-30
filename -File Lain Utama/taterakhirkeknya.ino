#include <WiFi.h>
#include <HTTPClient.h>
#include <ESP32Servo.h>

// Konfigurasi WiFi
#define WIFI_SSID "Suryadi Art Home"
#define WIFI_PASSWORD "mampirngombe20"

// URL Server PHP untuk update status pintu dan menyimpan data sensor
#define SERVER_URL "http://192.168.1.5/irigasiphp/simpan_data.php"

// Pin sensor ultrasonik
#define TRIG_PIN 18
#define ECHO_PIN 19

// Pin servo
Servo servo;
const int servoPin = 2;

// Konfigurasi batas ketinggian air
const float MAX_DISTANCE = 20;  // Jika air lebih dekat dari ini, pintu terbuka
const float NORMAL_DISTANCE = 50; // Jika air lebih jauh dari ini, pintu tertutup

bool gateOpen = false;

void setup() {
    Serial.begin(115200);

    // Menghubungkan ke WiFi
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
    while (WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
    }
    Serial.println("\nTerhubung ke WiFi!");

    // Inisialisasi servo
    servo.attach(servoPin);
    closeGate(); // Pastikan pintu awalnya tertutup

    // Inisialisasi sensor ultrasonik
    pinMode(TRIG_PIN, OUTPUT);
    pinMode(ECHO_PIN, INPUT);
}

// Fungsi untuk mendapatkan ketinggian air menggunakan sensor ultrasonik
float getWaterLevel() {
    digitalWrite(TRIG_PIN, LOW);
    delayMicroseconds(2);
    digitalWrite(TRIG_PIN, HIGH);
    delayMicroseconds(10);
    digitalWrite(TRIG_PIN, LOW);

    long duration = pulseIn(ECHO_PIN, HIGH);
    float distance = (duration * 0.0343) / 2; // Konversi ke cm

    Serial.print("Ketinggian air: ");
    Serial.print(distance);
    Serial.println(" cm");

    return distance;
}

// Fungsi untuk mengirim data ke server
void sendDataToServer(float waterLevel, String status) {
    if (WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        http.begin(SERVER_URL);
        http.addHeader("Content-Type", "application/x-www-form-urlencoded");

        String httpRequestData = "ketinggian=" + String(waterLevel) + "&status=" + status;
        Serial.print("Mengirim data: ");
        Serial.println(httpRequestData);  // Lihat apakah nilai sensor = 0

        int httpResponseCode = http.POST(httpRequestData);

        if (httpResponseCode > 0) {
            String response = http.getString();
            Serial.print("Respons Server: ");
            Serial.println(response); // Lihat respons dari server
        } else {
            Serial.print("Error HTTP: ");
            Serial.println(httpResponseCode);
        }

        http.end();
    } else {
        Serial.println("Gagal terhubung ke server!");
    }
}

// Fungsi untuk membuka pintu air
void openGate() {
    servo.write(180);
    gateOpen = true;
    Serial.println("Pintu terbuka karena ketinggian air tinggi!");
    sendDataToServer(getWaterLevel(), "Terbuka oleh Sensor");
}

// Fungsi untuk menutup pintu air
void closeGate() {
    servo.write(0);
    gateOpen = false;
    Serial.println("Pintu tertutup kembali!");
    sendDataToServer(getWaterLevel(), "Tertutup oleh Sensor");
}

void loop() {
    float waterLevel = getWaterLevel();

    // Jika ketinggian air lebih tinggi dari batas, buka pintu
    if (waterLevel <= MAX_DISTANCE && !gateOpen) {
        openGate();
        Serial.println("Pintu terbuka karena ketinggian air tinggi!");
    }
    
    // Jika air kembali normal, tutup pintu
    if (waterLevel >= NORMAL_DISTANCE && gateOpen) {
        closeGate();
        Serial.println("Pintu tertutup kembali!");
    }

    delay(500);
}
