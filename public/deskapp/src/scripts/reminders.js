/**
 * Reminders System - Native Browser Notifications
 */

const ReminderSystem = {
    pollingInterval: 60000, // Check every minute
    isSubscribed: false,

    init() {
        if (!("Notification" in window)) {
            console.log("This browser does not support desktop notification");
            return;
        }

        this.checkPermission();
        this.startPolling();
    },

    checkPermission() {
        if (Notification.permission === "granted") {
            this.isSubscribed = true;
        }
    },

    requestPermission() {
        Notification.requestPermission().then((permission) => {
            if (permission === "granted") {
                this.isSubscribed = true;
                this.showNotification("Notifications Enabled", {
                    body: "You will now receive trading reminders."
                });
            }
        });
    },

    startPolling() {
        // First check
        this.poll();

        // Interval
        setInterval(() => this.poll(), this.pollingInterval);
    },

    async poll() {
        if (!this.isSubscribed) return;

        try {
            const response = await fetch('/reminders/upcoming');
            const reminders = await response.json();

            reminders.forEach(reminder => {
                this.processReminder(reminder);
            });
        } catch (error) {
            console.error("Error fetching upcoming reminders:", error);
        }
    },

    processReminder(reminder) {
        const now = new Date();

        // Check expiration
        if (reminder.expires_at) {
            const expiresAt = new Date(reminder.expires_at);
            if (now > expiresAt) return; // Expired
        }

        // Handle One-off (Timed)
        if (reminder.frequency === 'once' && reminder.remind_at) {
            const remindAt = new Date(reminder.remind_at);
            // If it's due now (within current minute)
            if (Math.abs(now - remindAt) < 60000) {
                this.showNotification(reminder.title, {
                    body: reminder.content,
                    tag: `reminder-${reminder.id}`
                });
            }
        }

        // Handle Recurring
        else if (reminder.frequency !== 'once') {
            this.handleRecurrence(reminder);
        }
    },

    handleRecurrence(reminder) {
        const now = new Date();
        const lastReminded = reminder.last_reminded_at ? new Date(reminder.last_reminded_at) : null;

        // Check expiration for recurring as well
        if (reminder.expires_at) {
            const expiresAt = new Date(reminder.expires_at);
            if (now > expiresAt) return;
        }

        let shouldShow = false;

        if (reminder.frequency === 'daily') {
            const currentHour = now.getHours();
            const selectedHours = Array.isArray(reminder.recurrence_days) ? reminder.recurrence_days : [];

            if (selectedHours.length > 0) {
                // Hour-based daily: Check if current hour is selected
                if (selectedHours.includes(currentHour.toString()) || selectedHours.includes(currentHour)) {
                    // Only notify if we haven't notified in THIS specific hour today
                    if (!lastReminded ||
                        lastReminded.toDateString() !== now.toDateString() ||
                        lastReminded.getHours() !== currentHour) {
                        shouldShow = true;
                    }
                }
            } else {
                // Simple daily: once per day
                if (!lastReminded || lastReminded.toDateString() !== now.toDateString()) {
                    shouldShow = true;
                }
            }
        } else if (reminder.frequency === 'weekly') {
            const created = new Date(reminder.created_at);
            if (now.getDay() === created.getDay()) {
                if (!lastReminded || lastReminded.toDateString() !== now.toDateString()) {
                    shouldShow = true;
                }
            }
        } else if (reminder.frequency === 'custom' && reminder.recurrence_days) {
            const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const todayName = dayNames[now.getDay()];
            if (reminder.recurrence_days.includes(todayName)) {
                if (!lastReminded || lastReminded.toDateString() !== now.toDateString()) {
                    shouldShow = true;
                }
            }
        }

        if (shouldShow) {
            this.showNotification(reminder.title, {
                body: reminder.content,
                tag: `reminder-recurring-${reminder.id}`
            }, reminder);
        }
    },

    showNotification(title, options, reminder = null) {
        // 1. Native Browser Notification
        if (Notification.permission === "granted") {
            const notification = new Notification(title, options);
            notification.onclick = function () {
                window.focus();
                this.close();
            };
        }

        // 2. In-App Timer Popup (at the top)
        this.showPopup(title, options.body);

        // 3. Notify Backend
        if (reminder && reminder.id) {
            this.markAsNotified(reminder.id);
        }
    },

    async markAsNotified(reminderId) {
        try {
            await fetch(`/reminders/${reminderId}/notified`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
        } catch (error) {
            console.error("Error marking reminder as notified:", error);
        }
    },

    showPopup(title, content) {
        const popup = document.getElementById('reminder-top-popup');
        const pTitle = document.getElementById('popup-title');
        const pContent = document.getElementById('popup-content');

        if (popup && pTitle && pContent) {
            pTitle.innerText = title;
            pContent.innerText = content || '';
            popup.classList.add('show');

            // Auto-hide after 10 seconds
            setTimeout(() => {
                popup.classList.remove('show');
            }, 10000);
        }
    }
};

// Initialize if on a page that needs it
document.addEventListener('DOMContentLoaded', () => {
    ReminderSystem.init();
});
