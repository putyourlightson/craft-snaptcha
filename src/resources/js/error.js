document.getElementById('warning').remove();
document.getElementById('error').classList.remove('hidden');
document.getElementById('graphic').classList.remove('hidden');

setTimeout(function() {
    document.getElementById('graphic').remove();
    document.getElementById('resubmit').classList.remove('disabled');
}, document.getElementById('resubmit').dataset.timeout * 1000);
