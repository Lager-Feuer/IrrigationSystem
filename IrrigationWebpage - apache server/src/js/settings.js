const syncInterval = document.getElementById("syncInterval");
const lowerTriggerValue = document.getElementById("lowerTriggerValue");
const upperTriggerValue = document.getElementById("upperTriggerValue");
const notificationTriggervalue = document.getElementById("notificationTriggervalue");
const pump1Activated = document.getElementById("pump1Activated");
const pump2Activated = document.getElementById("pump2Activated");
const lastSentMoistureLvl = document.getElementById("lastSentMoistureLvl");
const maintenanceActive = document.getElementById("maintenanceActive");
const pump1Force = document.getElementById("pump1Force");
const pump2Force = document.getElementById("pump2Force");
const testNotifications = document.getElementById("testNotifications");

loadSettings();

function openSettings() {
    document.getElementById("settings").classList.add("settingsOpen");
}

function closeSettings() {
    document.getElementById("settings").classList.remove("settingsOpen");
}

function loadSettings() {
    $.ajax({
        method: "GET",
        dataType: "html",
        url: "/php/settings.php",
        success: function(data) {
            var settings = JSON.parse(data);
            for (var i in settings) {
                syncInterval.value = settings[i].syncInterval;
                lowerTriggerValue.value = settings[i].lowerTriggerValue;
                upperTriggerValue.value = settings[i].upperTriggerValue;
                notificationTriggervalue.value = settings[i].notificationTriggerValue;
                lastSentMoistureLvl.value = settings[i].lastSentMoistureLvl;

                if (settings[i].pump1Activated == 0) pump1Activated.value = false;
                else pump1Activated.value = true;

                if (settings[i].pump2Activated == 0) pump2Activated.value = false;
                else pump2Activated.value = true;

                maintenanceActive.checked = settings[i].maintenanceActive;
                pump1Force.checked = settings[i].pump1Force;
                pump2Force.checked = settings[i].pump2Force;
                testNotifications.checked = settings[i].testNotifications;

                changeMaintenance();
            }
        },
        error: function(error) {
            alert('Error occured: ' + error);
        }
    })
}

function saveSettings() {

    if( !validateForm() )
    {
        alert("Synchronisierungs-Interval - Werte von 0 bis 30 in Sekunden\nAlle Schwellenwerte - Werte von 0 bis 100 in Prozent (%)");
        return;
    }
    $.ajax({
        method: "POST",
        dataType: "html",
        url: "/php/saveSettings.php",
        data: {
            syncInterval: syncInterval.value,
            lowerTriggerValue: lowerTriggerValue.value,
            upperTriggerValue: upperTriggerValue.value,
            notificationTriggerValue: notificationTriggervalue.value,
            maintenanceActive: Number(maintenanceActive.checked),
            pump1Force: Number(pump1Force.checked),
            pump2Force: Number(pump2Force.checked),
            testNotifications: Number(testNotifications.checked)
        },
        success: function (data) {
            alert("Saved settings.");
            loadSettings();
        },
        error: function (error) {
            alert('Error occured: ' + error);
        }
    })
}

function changeMaintenance()
{
    if (maintenanceActive.checked) {
        pump1Force.disabled = false;
        pump2Force.disabled = false;
        testNotifications.disabled = false;
    } else {
        pump1Force.disabled = true;
        pump2Force.disabled = true;
        testNotifications.disabled = true;
    }
}

function validateForm() {
    if(syncInterval.value > 30 || syncInterval.value < 0) return false;
    if(lowerTriggerValue.value > 100 || lowerTriggerValue.value < 0) return false;
    if(upperTriggerValue.value > 100 || upperTriggerValue.value < 0) return false;
    if(notificationTriggervalue.value > 100 || notificationTriggervalue.value < 0) return false;

    return true;
}

window.onclick = function (event) {
    if (event.target == document.getElementById("settings")) {
        closeSettings();
    }
}