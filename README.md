# Irrigation system

A school project, which shows received data from an Arduino irrigation system.
You can also choose to opt-in on web push notifiactions.

# IrrigationClient
IrrigationClient contains the c++ main code on the wemos D1 Mini microcontroller (ESP8266), which is responsible to send data from the Arduino Uno to the linux server, where the data is stored.
- It also routes commands from the linux server to the Arduino Uno in order to enable/disable the connected pumps.
- The connection to the Arduino Uno is implemented over i2c wire connection.
- The Wemos D1 Mini is the master.

# IrrigationMusic
IrrigationMusic contains the code for the Arduino Uno, which loads and plays music from an sd card.

# IrrigationPumpController
IrrigationPumpController contains the code for the Arduino, where the pumps and moisture sensor are connected to.
- The ArduinUno waits for request and commands from the i2c master Wemos D1 Mini.
- It also listens for a self-destroy button, which enables pump 2.
- The connection to the Wemos D1 Mini is implemented over i2c wire connection.
- The Arduino Uno is the slave.

# IrrigationServer
IrrigationServer contains the tls encrypted tcp server socket in PHP, which the IrrigationClient connects to.
- It receives data from IrrigationClient. The data is collected from IrrigationPumpController.
- It sends commands to IrrigationClient, which then are routed to IrrigationPumpController.
- It stores received data in database.
- Sends notifications to subscribed users.

# IrrigationWebpage
IrrigationWebpage contains the website with the graph and settings.
- Graph for showing the history of humidity levels.
- Push-Notifications.
- Settings for controlling the system and trigger-values.
- Displaying the status of the pumps and the system.
- Maintenance mode.
- Control the pumps manually over the maintenance mode.
- Test push-notifications over the maintenance mode

# Libraries
Libraries contains the own written c++ libraries, which are written for the ESP8266 and Arduino Uno

## IrrigationSocket
The IrrigationSocket library contains the c++ code for connecting to the server, receiving data and sending data.

## Pump
The Pump library contains the c++ code for setting up and controlling pumps, which are connected to the Arduino Uno

## MasterWire
The MasterWire library contains the c++ code for receiving data and sending data to the IrrigationPumpController over i2c connection.
