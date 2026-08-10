export function useConfirmModal() {

    const confirm = async (message) => {

        const modalElem = document.createElement('div');

        modalElem.className = 'modal';
        modalElem.id = 'modal-confirm';

        modalElem.innerHTML = `
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                <div class="modal-body text-center pt-4">
                    <div style="width: 50px; height: 50px; margin: 0 auto; background-color: #f8d7da; border-color: #f8d7da;" class=" rounded-circle d-flex align-items-center justify-content-center mb-3">
                        <i class="far fa-trash-can text-danger fs-3"></i>
                    </div>
                    <h5 class="mb-4">Confirmation</h5>
                    <span>${message}</span>
                </div>
                <div class="modal-footer border-none bg-white justify-content-center gap-2">
                    <button id="modal-btn-cancel" type="button" class="btn btn-secondary btn-sm">Cancel</button>
                    <button id="modal-btn-accept" type="button" class="btn btn-danger btn-sm">Delete</button>
                </div>
                </div>
            </div>
            `;

        const modal = new bootstrap.Modal(modalElem, {
            keyboard: false,
            backdrop: 'static'
        });

        // Append modal to body and show it
        document.body.appendChild(modalElem);
        modal.show();

        return new Promise((resolve) => {
            const response = (e) => {
                let bool = false;
                if (e.target.id === 'modal-btn-cancel') bool = false;
                else if (e.target.id === 'modal-btn-accept') bool = true;
                else return;

                // Cleanup event listener and modal
                document.body.removeEventListener('click', response);
                modal.hide();
                modalElem.remove();
                resolve(bool);
            };

            document.body.addEventListener('click', response);
        });
    };

    return {
        confirm,
    };
}
