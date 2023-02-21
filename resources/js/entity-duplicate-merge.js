import $ from 'jquery';
import Swal from 'sweetalert2';

$(document).ready(function () {

    let entityTypeSelect = $('.js-entity-type-select'),
        getListUrl = entityTypeSelect.data('action-get-list'),
        getEntityFormUrl = entityTypeSelect.data('action-get-entity-form'),
        mergeContainer = $('.js-merge-container'),
        mergeBody = $('.js-merge-body'),
        submitMergeBtn = $('.js-submit-merge-btn'),
        entityType,
        masterId,
        secondaryId;

    const masterChoices = new Choices('.js-master-entity-field');
    const secondaryChoices = new Choices('.js-secondary-entity-field');

    entityTypeSelect.on('change', function () {

        resetPage();
        entityType = $(this).val();

        if (entityType === '') {
            return false;
        }

        $.getJSON(getListUrl, {'entity_type': entityType}, function (response) {
            if (response.status === 'success') {
                let entitiesList = response.data;
                entitiesList.push({
                    value: 0,
                    label: 'Select entity...',
                    selected: true,
                    disabled: true,
                });

                masterChoices.setChoices(entitiesList);
                secondaryChoices.setChoices(entitiesList);
                mergeContainer.show();
            }
        });
    });

    masterChoices.passedElement.element.addEventListener(
        'change',
        function(event) {
            masterId = event.detail.value;
            getBody();
        },
        false,
    );

    secondaryChoices.passedElement.element.addEventListener(
        'change',
        function(event) {
            secondaryId = event.detail.value;
            getBody();
        },
        false,
    );

    function getBody() {
        if (masterId === undefined || secondaryId === undefined) {
            return false;
        }

        if (masterId === secondaryId) {
            mergeBody.html('<div class="col-md-4 alert alert-danger">Can\'t merge same entities!</div>');
            return false;
        }

        $.getJSON(getEntityFormUrl, {
            'entity_type': entityType,
            'master_id': masterId,
            'secondary_id': secondaryId
        }, function (response) {
            if (response.status === 'success') {
                mergeBody.html(response.data);
                submitMergeBtn.attr('disabled', false);
            }
        });
    }

    function resetPage() {
        submitMergeBtn.attr('disabled', true);
        mergeContainer.hide();
        masterChoices.clearChoices();
        secondaryChoices.clearChoices();
        mergeBody.text('Choose 2 entities to start merging');

        masterId = undefined;
        secondaryId = undefined;
    }

    submitMergeBtn.on('click', function (e) {
        e.preventDefault();

        new Swal({
            title: "Are you sure?",
            text: "Your will not be able to recover secondary entity!",
            icon: "warning",
            buttons: {
                cancel: {
                    text: 'Cancel',
                    value: false,
                    visible: true,
                    className: "bg-secondary",
                    closeModal: true,
                },
                merge: {
                    text: "Merge",
                    value: true,
                    visible: true,
                    className: "bg-primary",
                }
            },
        }).then((value) => {
            if (value) {
                $('.js-entity-merge-form').submit();
            }
        });
    });
});
