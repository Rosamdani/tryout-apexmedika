let remainingTime = 0;
let interval = null;
let syncInterval = null;

onmessage = function (e) {
    const { type, time } = e.data;

    if (type === "start") {
        remainingTime = time;
        if (!interval) startCountdown();
    } else if (type === "pause") {
        clearInterval(interval);
        clearInterval(syncInterval);
        interval = null;
        syncInterval = null;
    }
};

function startCountdown() {
    interval = setInterval(() => {
        if (remainingTime > 0) {
            remainingTime -= 1;
            postMessage({ type: "time-update", remainingTime });
        } else {
            clearInterval(interval);
            postMessage({ type: "time-up" });
        }
    }, 1000);

    // Kirim pembaruan waktu ke server setiap menit
    syncInterval = setInterval(() => {
        postMessage({ type: "sync-time", remainingTime });
    }, 60000); // 60.000 ms = 1 menit
}
