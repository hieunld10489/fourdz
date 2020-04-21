<?php
if ($this->session->flashdata('error')) {
    renderError($this->session->flashdata('error'));
    $this->session->unset_userdata('error');
}

if ($this->session->flashdata('success')) {
    renderSuccess($this->session->flashdata('success'));
    $this->session->unset_userdata('success');
}
?>