// Function for Button: Subscripe browser/device for push notifications
function notifyButtonClick() {
    if (Notification.permission === "default") {
        Notification.requestPermission().then(perm => {
            if (Notification.permission === "granted") {
                regWorker().catch(err => console.error(err));
            } else {
                alert("Please allow notifications.");
            }
        });
    } else if (Notification.permission === "granted") {
        regWorker().catch(err => console.error(err));
    } else { alert("Please allow notifications."); }
}

async function regWorker() {
    const publicKey = "BNzNIThB0mHAgR-NzDKBfekQ4XD_WBauTUOwUfFZzKHAg5_HkMod1afp2M7sO-63rju-78zPJqmQSZuNvhYr9Qc";
    navigator.serviceWorker.register("pnotifications-serviceWorker.js", { scope: "/" });

    // Subscripe push notifications
    navigator.serviceWorker.ready
        .then(reg => {
            reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: publicKey
            }).then(
                sub => {
                    var data = new FormData();
                    data.append("sub", JSON.stringify(sub));
                    fetch("../php/saveSubscription.php", { method: "POST", body: data })
                        .then(res => res.text())
                        .then(txt => { alert(txt); })
                        .catch(err => console.error(err));
                },

                // Throw error, if subscription fails
                err => console.error(err)
            );
        });
}