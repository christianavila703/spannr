/* Close Edit Job Application Modal */
import * as bootstrap from "bootstrap";

if (document.getElementById('editJobApplicationModal')) {
    const editJobAppModal = new bootstrap.Modal('#editJobApplicationModal');
    window.addEventListener('closeEditJobAppModal', event => {
        editJobAppModal.hide();
    });
}

if (document.getElementById('jobDetailsModal')) {
    const jobDetailsModal = new bootstrap.Modal('#jobDetailsModal');
    window.addEventListener('showRecruitingJobDetailsModal', event => {
        jobDetailsModal.show();
    });
}
