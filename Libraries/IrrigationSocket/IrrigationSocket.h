/*
  IrrigationSocket.h - Library for sending data to IrrigationServer.
*/
#ifndef IRRIGATIONSOCKET_H_INCLUDED
#define IRRIGATIONSOCKET_H_INCLUDED
#pragma once
#include <Arduino.h>
#include <ArduinoJson.h>
#include <ESP8266WiFi.h>
#include <WiFiClient.h>
#include <WiFiClientSecure.h>

class IrrigationSocket
{
  public:
    IrrigationSocket(const String& ip, const int& port);
    ~IrrigationSocket();

    bool startConnection(const int& moistureLevel, const bool& pump1Activated, const bool& pump2Activated);
    bool connected();
    void sendData(const String& data);
    String receiveData();
    void stopConnection();

    /* ========= Getters and Setters ========= */
     WiFiClientSecure getClient();
    
    String getIP();
    void setIP(const String& ip);

    int getPort();
    void setPort(const int& port);

  private:
    WiFiClientSecure _client;
    String _ip;
    int _port;
};

#endif