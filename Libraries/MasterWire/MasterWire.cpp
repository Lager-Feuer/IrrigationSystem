/*
  MasterWire.cpp - Library for establishing an i2c connection to arduino uno and swapping data.
*/

#include <ArduinoJson.h>
#include <Arduino.h>
#include <Wire.h>
#include "MasterWire.h"

MasterWire::MasterWire(int channel)
{
  Wire.begin();
  _channel = channel;
}

void MasterWire::sendCommand(uint8_t commandCode = 0x00)
{
    Wire.beginTransmission(_channel);
    Wire.write(commandCode);
    Wire.endTransmission();
}

String MasterWire::getDataAsString(String dataKey)
{
    String json = "";
    StaticJsonDocument<256> doc;
    
    Wire.requestFrom(1, 32);

    while (Wire.available())
    {
     char c = Wire.read();
     json += c;
     Serial.print(c);
    }

    DeserializationError error = deserializeJson(doc, json);
    if (error) return error.f_str();

    return doc[dataKey];
}

bool MasterWire::getDataAsBool(String dataKey)
{
    String json = "";
    StaticJsonDocument<256> doc;
    
    Wire.requestFrom(1, 32);

    while (Wire.available())
    {
     char c = Wire.read();
     json += c;
     Serial.print(c);
    }

    DeserializationError error = deserializeJson(doc, json);
    if (error) return error.f_str();

    return doc[dataKey];
}

int MasterWire::getMoistureLevel()
{
    String json = "";
    StaticJsonDocument<256> doc;

    sendCommand(0x01);
    Wire.requestFrom(1, 32);

    while (Wire.available())
    {
     char c = Wire.read();
     json += c;
    }

    DeserializationError error = deserializeJson(doc, json);
    if (error) return 0;

    return doc["moistureLevel"];
}

bool MasterWire::getPump1Activated()
{
    int c = 0;
    StaticJsonDocument<256> doc;

    sendCommand(0x02);
    Wire.requestFrom(1, 1);
    c = Wire.read();

    if (c == 1) return true;
    return false;
}

bool MasterWire::getPump2Activated()
{
    int c = 0;
    StaticJsonDocument<256> doc;

    sendCommand(0x03);
    Wire.requestFrom(1, 1);

    c = Wire.read();

    if (c == 1) return true;
    return false;
}


int MasterWire::getChannel()
{
    return _channel;
}

void MasterWire::setChannel(int channel)
{
    _channel = channel;
}