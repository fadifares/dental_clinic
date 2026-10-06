import $ from 'jquery';
import * as bootstrap from 'bootstrap';

// Bind to window
const jQ = window.jQuery || $;
const bs = window.bootstrap || bootstrap;

window.$ = window.jQuery = jQ;
window.bootstrap = bs;

// Bootstrap 5 jQuery Adapter for Modal & Tab
if (jQ && jQ.fn) {
    jQ.fn.modal = function (action) {
        return this.each(function () {
            const modalInstance = bs.Modal.getOrCreateInstance(this);
            if (typeof action === 'string' && typeof modalInstance[action] === 'function') {
                modalInstance[action]();
            } else if (!action || action === 'show') {
                modalInstance.show();
            } else if (action === 'hide') {
                modalInstance.hide();
            }
        });
    };

    jQ.fn.tab = function (action) {
        return this.each(function () {
            const tabInstance = bs.Tab.getOrCreateInstance(this);
            if (typeof action === 'string' && typeof tabInstance[action] === 'function') {
                tabInstance[action]();
            } else if (!action || action === 'show') {
                tabInstance.show();
            }
        });
    };
}

// Global helper for opening tooth modal safely
window.openToothModal = function (toothId, toothName) {
    const modalEl = document.getElementById('toothActionModal');
    if (!modalEl) return;

    if ($('#activeToothNumber').length) {
        $('#activeToothNumber').val(toothId);
    }
    if ($('#selectedToothDisplay').length) {
        $('#selectedToothDisplay').text(`${toothId} - ${toothName}`);
    }

    const modal = bs.Modal.getOrCreateInstance(modalEl);
    modal.show();
};

window.closeToothModal = function () {
    const modalEl = document.getElementById('toothActionModal');
    if (!modalEl) return;
    const modal = bs.Modal.getOrCreateInstance(modalEl);
    modal.hide();
};

// Initialize on Document Ready
$(function () {
    console.log('🦷 Dental Clinic System initialized with jQuery and Bootstrap 5.');

    // Initialize all Bootstrap tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bs.Tooltip(tooltipTriggerEl));

    // Interactive Odontogram Tooth Selection Logic
    $(document).on('click', '.tooth-item', function () {
        const toothId = $(this).data('tooth-id');
        const toothName = $(this).data('tooth-name') || `السن رقم ${toothId}`;
        $('.tooth-item').removeClass('selected');
        $(this).addClass('selected');

        window.openToothModal(toothId, toothName);
    });

    // Tooth Condition Assignment
    $(document).on('click', '.btn-apply-tooth-condition', function () {
        const condition = $(this).data('condition');
        const activeTooth = $('.tooth-item.selected');
        if (activeTooth.length) {
            activeTooth.attr('data-status', condition);
            const statusLabel = $(this).text();
            activeTooth.find('.tooth-status-badge').text(statusLabel);
            window.closeToothModal();
        }
    });
});
