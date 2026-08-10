import { ref } from "vue";

const icons = {
    success: '<i class="fas fa-circle-check"></i>',
    danger: '<i class="fas fa-triangle-exclamation"></i>',
    warning: '<i class="fas fa-circle-exclamation"></i>',
    info: '<i class="fas fa-circle-info"></i>',
};

export function useToast() {
    const activeToast = ref(null);

    const showToast = (message = null, type = "info", options = {}) => {
        if (!Object.keys(icons).includes(type)) {
            type = "info";
        }

        const duration = options.duration || 1500;

        const box = document.createElement("div");

        box.classList.add("toast", `toast-${type}`);

        box.innerHTML = `<div class="toast-content-wrapper">
                            <div class="toast-icon">
                                ${icons[type]}
                            </div>
                            <div class="toast-message">${message}</div>
                        </div>`;

        box.style.animation = `toastSlideInRight 0.3s ease-in-out forwards, toastSlideOutRight 0.5s ease-in-out forwards ${duration / 1000}s`;

        const existingToast = document.querySelector(".toast");
        const interval = setTimeout(() => {
            options.onClose && options.onClose();
            box.remove();
            activeToast.value = null;
        }, duration + 500);

        if (existingToast) {
            clearTimeout(interval);
            existingToast.remove();
        }

        document.body.appendChild(box);
        activeToast.value = box;
    };

    return { showToast };
}
