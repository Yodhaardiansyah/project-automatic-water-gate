#include <WiFi.h>
#include <HTTPClient.h>
#include <ESP32Servo.h>

// ====================== KONFIGURASI ======================
// Ganti IP sesuai alamat server kamu
#define SERVER_IP "192.168.2.107:8000"

// WiFi
#define WIFI_SSID "Yoss"
#define WIFI_PASSWORD "06122002"

// URL dinamis berdasarkan IP server
String URL_SIMAPAN_DATA = "http://" + String(SERVER_IP) + "/simpan_data.php";
String URL_CEK_JADWAL   = "http://" + String(SERVER_IP) + "/cek_jadwal.php";

// Sensor ultrasonik
#define TRIG_PIN 5
#define ECHO_PIN 18

// Servo
Servo servo;
const int SERVO_PIN = 2;

// Logika ketinggian air
const float MAX_DISTANCE = 20;     // Pintu buka jika air < 20cm
const float NORMAL_DISTANCE = 50;  // Pintu tutup jika air > 50cm

bool gateOpen = false;

// ======================= SETUP =======================
void setup() {
    Serial.begin(115200);

    // Koneksi WiFi
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
    Serial.print("Menghubungkan ke WiFi");
    while (WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
    }
    Serial.println("\nTerhubung ke WiFi!");

    // Servo & sensor
    servo.attach(SERVO_PIN);
    closeGate(0); // Pastikan tertutup saat start

    pinMode(TRIG_PIN, OUTPUT);
    pinMode(ECHO_PIN, INPUT);
}

// ======================= LOOP ========================
void loop() {
    String status = getStatusJadwal();

    float waterLevel = getWaterLevel();
    if (waterLevel == -1) {
        delay(2000);
        return;
    }

    // Kirim data real-time ke server setiap loop
    sendDataToServer(waterLevel, gateOpen ? "Terbuka" : "Tertutup");

    if (status == "Terbuka") {
        if (!gateOpen) openGate(waterLevel); // Buka berdasarkan jadwal
    } else {
        if (waterLevel <= MAX_DISTANCE && !gateOpen) {
            openGate(waterLevel);
        } else if (waterLevel >= NORMAL_DISTANCE && gateOpen) {
            closeGate(waterLevel);
        }
    }

    delay(3000);
}


// ======================= FUNGSI ========================

// Sensor ultrasonik
float getWaterLevel() {
    digitalWrite(TRIG_PIN, LOW);
    delayMicroseconds(2);
    digitalWrite(TRIG_PIN, HIGH);
    delayMicroseconds(10);
    digitalWrite(TRIG_PIN, LOW);

    long duration = pulseIn(ECHO_PIN, HIGH, 30000);
    float distance = (duration * 0.0343) / 2;

    if (distance <= 0 || distance > 400) {
        Serial.println("Sensor error atau data tidak valid.");
        return -1;
    }

    Serial.print("Ketinggian air: ");
    Serial.print(distance);
    Serial.println(" cm");
    return distance;
}

// Kirim data ke server
void sendDataToServer(float waterLevel, String status) {
    if (WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        http.begin(URL_SIMAPAN_DATA);
        http.addHeader("Content-Type", "application/x-www-form-urlencoded");

        String data = "ketinggian=" + String(waterLevel) + "&status=" + status;
        Serial.print("Mengirim data: ");
        Serial.println(data);

        int httpCode = http.POST(data);
        if (httpCode > 0) {
            String response = http.getString();
            Serial.print("Respons Server: ");
            Serial.println(response);
        } else {
            Serial.print("Error HTTP: ");
            Serial.println(httpCode);
        }

        http.end();
    } else {
        Serial.println("Gagal terhubung ke server!");
    }
}

// Buka pintu
void openGate(float level) {
    servo.write(180);
    gateOpen = true;
    Serial.println("Pintu terbuka!");
    sendDataToServer(level, "Terbuka oleh Sensor");
}

// Tutup pintu
void closeGate(float level) {
    servo.write(0);
    gateOpen = false;
    Serial.println("Pintu tertutup!");
    sendDataToServer(level, "Tertutup oleh Sensor");
}

// Ambil status dari jadwal
String getStatusJadwal() {
    HTTPClient http;
    http.begin(URL_CEK_JADWAL);

    int httpCode = http.GET();
    String status = "";

    if (httpCode > 0) {
        status = http.getString();
        status.trim();
        Serial.print("Status dari jadwal: ");
        Serial.println(status);
    } else {
        Serial.print("Gagal akses jadwal: ");
        Serial.println(httpCode);
    }

    http.end();
    return status;
}
