self.addEventListener("push", evt => {
    const data = evt.data.json();
    self.registration.showNotification(data.title, {
        body: data.body,
        icon: data.icon,
        image: data.image
    });
});