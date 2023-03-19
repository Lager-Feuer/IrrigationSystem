/*
  Pump.h - Library for controlling pumps connected to an Arduino Uno.
*/
#ifndef PUMP_H_INCLUDED
#define PUMP_H_INCLUDED
#pragma once
#include <Arduino.h>
#include <Wire.h>

class Pump
{
  public:
    Pump(const int& pin);
    ~Pump();

    // Pump controls
    void activate();
    void deactivate();

    // Getters and Setters
    int getActivated();

    int getPin();
    void setPin(const int& pin);


  private:
    int _pin;
    bool _activated;
};

#endif