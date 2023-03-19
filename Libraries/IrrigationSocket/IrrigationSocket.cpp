/*
  IrrigationSocket.cpp - Library for sending data to IrrigationServer.
*/

#include <Arduino.h>
#include <ArduinoJson.h>
#include <ESP8266WiFi.h>
#include <WiFiClient.h>
#include <WiFiClientSecure.h>
#include "IrrigationSocket.h"

IrrigationSocket::IrrigationSocket(const String& ip = "20.4.228.109", const int& port = 2655)
{
  _ip = ip;
  _port = port;
}

IrrigationSocket::~IrrigationSocket()
{
  if(_client) _client.stop();
}

bool IrrigationSocket::startConnection(const int& moistureLevel, const bool& pump1Activated, const bool& pump2Activated)
{
  Serial.println("Connecting to the server.");

  _client.setInsecure();
  if (!_client.connect(_ip.c_str(), _port))
  {
    Serial.println("Connection failed.");
    return false;
  }
  
  String jsonString;
  StaticJsonDocument<256> docData;
  docData["deviceName"] = "irrigationClient";
  docData["applicationKey"] = "dHe4cpD4Iv8dT3uvCxRo";

  serializeJson(docData, jsonString);
  _client.print(jsonString + "\n");

  if (_client.available())
  {
    
    deserializeJson(docData, _client);
    if ( strcmp( docData["status"], "success" ) == 0 )
    {
      docData.clear();
      jsonString.clear();
      docData["pump1Activated"] = pump1Activated;
      docData["pump2Activated"] = pump2Activated;
      docData["moistureLevel"] = moistureLevel;
        
      serializeJson(docData, jsonString);
      sendData(jsonString + "\n");

      return true;
    }
  }
  return false;
}

bool IrrigationSocket::connected()
{
  if(_client) return _client.connected();
  return false;
}

void IrrigationSocket::sendData(const String& data)
{
  _client.print(data);
}

String IrrigationSocket::receiveData()
{
  StaticJsonDocument<256> docData;
  String jsonString = "";
  if (_client.available())
  {
      Serial.print("Server response: ");
      deserializeJson(docData, _client);
      serializeJson(docData, jsonString);
  }
  return jsonString;
}

void IrrigationSocket::stopConnection()
{
  if(_client) _client.stop();
}

/* ========= Getters and Setters ========= */

String IrrigationSocket::getIP()
{
    return _ip;
}

void IrrigationSocket::setIP(const String& ip)
{
    _ip = ip;
}

int IrrigationSocket::getPort()
{
  return _port;
}

void IrrigationSocket::setPort(const int& port)
{
    _port = port;
}