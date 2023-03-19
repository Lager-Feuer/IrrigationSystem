/*
  Pump.cpp - Library for controlling pumps connected to an Arduino Uno.
*/
#include <Wire.h>
#include <Arduino.h>
#include "Pump.h"

Pump::Pump(const int& pin)
{
    setPin(pin);
    deactivate();
}
Pump::~Pump()
{
    deactivate();
}

int Pump::getActivated()
{
    if(_activated) return 1;
    return 0;
}

int Pump::getPin()
{
    return _pin;
}

void Pump::setPin(const int& pin)
{
    _pin = pin;
    pinMode(_pin, OUTPUT);
}

void Pump::activate()
{
    digitalWrite(_pin, HIGH);
    _activated = true;
}

void Pump::deactivate()
{
    digitalWrite(_pin, LOW);
    _activated = false;
}