<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Starred Assignment</title>

        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet"/>
        <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet"/>

        <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    </head>
    <body>
        <div class="container">
            <?php
            $message = $this->session->flashdata('message');
            $error = $this->session->flashdata('error');
            if (!empty($message)) {
                ?>
                <div class="container">
                    <div class="alert alert-success alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <?php echo $message; ?>
                    </div>
                </div>
            <?php } ?>
            <?php if (!empty($error)) { ?>
                <div class="container">
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <?= $error; ?>
                    </div>
                </div>
            <?php } ?>
            <?php
            $validation_errors = validation_errors();
            if (!empty($validation_errors)) {
                ?>
                <div class="row">
                    <div class="container">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <h3 class="validation-errors"><?php echo validation_errors(); ?></h3>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-2 no-padding-left"><a href="<?= base_url('dashboard') ?>"><img src="<?= base_url('assets/images/StarredLogo.jpg') ?>"/></a></div>
                    <div class="col-md-10"><h1>Starred Assignment</h1></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <hr/>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <fieldset>
                        <legend>Create new job</legend>
                        <form method="POST" action="<?= base_url('dashboard/addJob') ?>">
                            <div class="col-md-8 form-group">
                                <input class="form-control" name="url_address" placeholder="Provide your URL" required="required"/>
                            </div>
                            <div class="col-md-4 form-group">
                                <input style="float: right" class="btn btn-info" type="submit" value="Create new Job"/>
                            </div>
                        </form>
                    </fieldset>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <hr/>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <a style="float: right" target="_blank" onclick="return confirm('The process will take some time. Are you sure?')" href="<?= base_url('dashboard/execute') ?>" class="btn btn-warning">
                        Run Process
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <fieldset>
                        <legend>Saved Jobs</legend>
                        <table id="jobs" class="table table-striped table-bordered table-condensed table-hover dataTable no-footer">
                            <thead>
                                <tr>
                                    <th>UUID</th>
                                    <th>URL</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($jobs)) { ?>
                                    <?php foreach ($jobs as $job) { ?>
                                        <tr class="job <?= $job['job_status'] ?>">
                                            <td><?= $job['uuid'] ?></td>
                                            <td><?= $job['url_address'] ?></td>
                                            <td><?= $job['job_status'] ?></td>
                                            <td><?= $job['job_created_date'] ?></td>
                                        </tr>
                                        <?php if (isset($job['urls']) && !empty($job['urls']) && $job['job_status'] != 'in_progress') { ?>
                                            <?php foreach ($job['urls'] as $url) { ?>
                                                <tr class="found-urls">
                                                    <td colspan="2"><?= $url['url_address'] ?></td>
                                                    <td colspan="2">
                                                        <?php if (isset($url['emails']) && !empty($url['emails'])) { ?>
                                                            <?php foreach ($url['emails'] as $email) { ?>
                                                                <?= $email['email_address'] . "<br/>" ?>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            No emails found
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </fieldset>
                </div>
            </div>

        </div>
        <script type="text/javascript">
            $(window).load(function () {
                $('#jobs').DataTable();
            });
        </script>
    </body>
</html>