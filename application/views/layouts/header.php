<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title>BASE</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<meta http-equiv="cache-control" content="max-age=0" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="copyright" content="CCS" />
	<meta name="revisit" content="5 days" />
	<meta name="Author" content="gffabio" />
	<meta name="medium" content="medium_type" />
	<meta name="Author Email" content="fabio.grandas@ccs.org.co" />
	<meta name="DC.creator" content="gffabio" />
	<meta name="DC.date" content="2020-04-22 010:00:00 AM" />
	<meta name="DC.language" content="ES" />
	<link rel="icon" type="image/png" href="<?php echo IP_SERVER ?>icon.png">
	<link rel="shortcut icon" href="<?php echo IP_SERVER ?>favicon.ico" title="CCS" id="CCS" type="image/x-icon" />
	<link href="<?php echo IP_SERVER ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="<?php echo IP_SERVER ?>assets/fontawesome/css/all.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo IP_SERVER ?>assets/sweetalert2/sweetalert2.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo IP_SERVER ?>assets/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo IP_SERVER ?>assets/css/main.css?<?php echo rand() ?>" rel="stylesheet" type="text/css" />
	<script src="<?php echo IP_SERVER ?>assets/jquery/jquery.min.js"></script>
	<script src="<?php echo IP_SERVER ?>assets/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script>
		var IP_SERVER = '<?php echo IP_SERVER ?>';
	</script>
</head>
<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        
        <!-- Sidebar -->
        <?php $this->load->view('admin/partials/sidebar'); ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php $this->load->view('admin/partials/topbar'); ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    
                    <!-- Flash Messages -->
                    <?php if ($this->session->flashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= $this->session->flashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?= $this->session->flashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

<style>
/* Adjust content to not be hidden behind the fixed sidebar */
#content-wrapper {
    /* Dynamic margin based on page */
    margin-left: <?= ($this->uri->segment(2) == 'dashboard' || $this->uri->segment(2) == '') ? '250px' : '80px' ?>;
    width: calc(100% - <?= ($this->uri->segment(2) == 'dashboard' || $this->uri->segment(2) == '') ? '250px' : '80px' ?>);
    transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@media (max-width: 768px) {
    #content-wrapper {
        margin-left: 0;
        width: 100%;
    }
}
</style>