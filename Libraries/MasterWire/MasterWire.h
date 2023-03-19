/*
  MasterWire.h - Library for establishing an i2c connection to arduino uno and swapping data.
*/
#ifndef MASTERWIRE_H_INCLUDED
#define MASTERWIRE_H_INCLUDED
#pragma once
#include <Arduino.h>

class MasterWire
{
  public:
    MasterWire(int channel);

    // Getters and Setters
    void sendCommand(uint8_t commandCode);
    String getDataAsString(String dataKey);
    bool getDataAsBool(String dataKey);

    int getMoistureLevel();
    bool getPump1Activated();
    bool getPump2Activated();
    
    int getChannel();
    void setChannel(int channel);

  private:
    int _channel;
};

#endif