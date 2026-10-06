import $ from 'jquery';
import * as bootstrap from 'bootstrap';

window.$ = window.jQuery = $;
window.bootstrap = bootstrap;

// Initialize on Document Ready
$(function () {
    console.log('🦷 Dental Clinic System initialized with jQuery and Bootstrap 5.');

    // Initialize all Bootstrap tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Interactive Odontogram Tooth Selection Logic
    $(document).on('click', '.tooth-item', function () {
        const toothId = $(this).data('tooth-id');
        const toothName = $(this).data('tooth-name');
        $('.tooth-item').removeClass('selected');
        $(this).addClass('selected');

        $('#selectedToothDisplay').text(`${toothId} - ${toothName}`);
        $('#toothActionModal').modal('show');
    });

    // Tooth Condition Assignment
    $(document).on('click', '.btn-apply-tooth-condition', function () {
        const condition = $(this).data('condition');
        const activeTooth = $('.tooth-item.selected');
        if (activeTooth.length) {
            activeTooth.attr('data-status', condition);
            const statusLabel = $(this).text();
            activeTooth.find('.tooth-status-badge').text(statusLabel);
            $('#toothActionModal').modal('hide');
        }
    });
});
