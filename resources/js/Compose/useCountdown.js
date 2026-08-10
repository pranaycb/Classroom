import { ref, onMounted, onBeforeUnmount } from 'vue';

/**
 * A composable for count down timer
 */
export default function useCountdown(end, callback) {

    const timeRemaining = ref({
        days: 0,
        hours: 0,
        minutes: 0,
        seconds: 0,
    });

    let countdownInterval = null;

    const calculateTimeRemaining = () => {
        const now = new Date();
        const diff = new Date(end) - now;

        if (diff > 0) {
            timeRemaining.value = {
                days: Math.floor(diff / (1000 * 60 * 60 * 24)),
                hours: Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
                minutes: Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)),
                seconds: Math.floor((diff % (1000 * 60)) / 1000),
            };
        }
        else {
            timeRemaining.value = { days: 0, hours: 0, minutes: 0, seconds: 0 };
            clearInterval(countdownInterval);
            callback();
        }
    };

    onMounted(() => {
        calculateTimeRemaining();
        countdownInterval = setInterval(calculateTimeRemaining, 1000);
    });

    onBeforeUnmount(() => {
        clearInterval(countdownInterval);
    });

    return { timeRemaining };
}
