#include <ArduinoJson.h>
#include <Wire.h>
#include <Pump.h>

uint8_t sentCMD = 0x00;
const int buttonPin = 4;
const int moistureSensorPin = A0;

int moistureThreshold = 200;
Pump pump1(2);
Pump pump2(3);

void setup() {
  Serial.begin(9600);

  pinMode(buttonPin, INPUT_PULLUP); // Enable internal pull-up resistor for button pin
  pinMode(moistureSensorPin, INPUT);
  
  // Start listening for requests and commands from master
  Wire.begin(1);
  Wire.onRequest(requestEvent);
  Wire.onReceive(receiveData);
}

void loop() {
  delay(1000);
  if (digitalRead(buttonPin) == LOW) {
    pump1.deactivate();
    pump2.activate();

    delay(10000);
    pump1.activate();
    pump2.deactivate();
    
    delay(5000);
    pump1.deactivate();
  }
}

void requestEvent() {
  StaticJsonDocument<256> doc;
  int moistureLevel = analogRead(moistureSensorPin);

  char json[256];
  if ( sentCMD == 0x01) 
  {
    doc["moistureLevel"] = moistureLevel;
    serializeJson(doc, json);

    Serial.print("sent: ");
    Serial.print(sizeof(json));
    Serial.print(" : ");
    Serial.println(json);
    Wire.write(json);
  }
  
  if( sentCMD == 0x02) 
  {
    int activated = pump1.getActivated();
    Wire.write(activated);
  }
  
  if( sentCMD == 0x03) 
  {
    int activated = pump2.getActivated();
    Wire.write(activated);
  }
  
}

void receiveData(int byteCount) {
  uint8_t command = Wire.read();
  Serial.print("commmand ");
  Serial.print(command);
  Serial.print(": ");
  sentCMD = command;

  if ( sentCMD  == 0x04) pump1.activate();
  if ( sentCMD  == 0x05) pump1.deactivate();
  if ( sentCMD  == 0x06) pump2.activate();
  if ( sentCMD  == 0x07) pump2.deactivate();
}
