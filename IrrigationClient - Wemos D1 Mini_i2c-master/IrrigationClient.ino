#include <ESP8266WiFi.h>
#include <WiFiClient.h>
#include <WiFiClientSecure.h>
#include <ArduinoJson.h>
#include "IrrigationSocket.h"
#include "MasterWire.h"

const char* ssid = "NetworkName";
const char* password = "NetworkPassword";

IrrigationSocket clientSocket("<ServerIP>", <ServerPort>);
MasterWire master1(1);

void setup()
{
  Serial.begin(115200);

  connectToWifi();

  Serial.println("Starting a new connection");
  clientSocket.startConnection( master1.getMoistureLevel(), master1.getPump1Activated(), master1.getPump2Activated() );
}

void loop() {
  delay(5000);

  if(!clientSocket.connected())
  {
    Serial.println("Lost connection. Reconnecting.");
    clientSocket.startConnection( master1.getMoistureLevel(), master1.getPump1Activated(), master1.getPump2Activated() );
    return;
  }

  while (clientSocket.connected())
  {
    Serial.println("wait 3 seconds");
    delay(3000);
    
    Serial.println("Check for command: ");
    String jsonString = clientSocket.receiveData();
    
    if( strcmp(jsonString.c_str(), "") == 0 ) 
    {
      Serial.println("Nothing to do.");
      continue;
    }
    Serial.println("json received: " + jsonString);
    
    StaticJsonDocument<256> docData;
    deserializeJson(docData, jsonString);
    String rcvCmd = docData["command"];
      
    if ( strcmp( rcvCmd.c_str(), "getData" ) == 0 ) 
    {
      docData.clear();
      int moistureLevel = master1.getMoistureLevel();
      bool activated1 = master1.getPump1Activated();
      bool activated2 = master1.getPump2Activated();

      docData["pump1Activated"] = activated1;
      docData["pump2Activated"] = activated2;
      docData["moistureLevel"] = moistureLevel;

      jsonString.clear();
      serializeJson(docData, jsonString);

      clientSocket.sendData(jsonString + "\n");
      Serial.print("Sent DATA.");
      Serial.println(jsonString);

      delay(500);
      rcvCmd.clear();
      docData.clear();

      String jsonString = clientSocket.receiveData();
      deserializeJson(docData, jsonString);
      String rcvCmd = docData["command"];
    }
    if ( strcmp( rcvCmd.c_str(), "activatePump1" ) == 0 ) master1.sendCommand(0x04);
    if ( strcmp( rcvCmd.c_str(), "deactivatePump1" ) == 0 ) master1.sendCommand(0x05);
    if ( strcmp( rcvCmd.c_str(), "activatePump2" ) == 0 ) master1.sendCommand(0x06);
    if ( strcmp( rcvCmd.c_str(), "deactivatePump2" ) == 0 ) master1.sendCommand(0x07);
    {

    }
  }
}

void connectToWifi()
{
  Serial.print("Connecting to ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);
  int i = 0;

  while (WiFi.status() != WL_CONNECTED)
  {
    delay(500);
    if ( i < 9) Serial.print(".");
    else
    {
      i = 0;
      Serial.println(".");
    }
    i += 1;
  }

  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());
}