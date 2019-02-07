<div class="page-content">

    <div class="page-head">

        <?= $this->load->view('include/page_head') ?>

    </div>

    <?= $this->load->view('include/page_breadcrumb') ?>

    <div class="row">

        <div class="col-md-12">

            <div class="portlet light bordered">

                <?= $this->load->view('include/portlet_title') ?>

                <div class="portlet-body flip-scroll">

                    <?= $this->load->view('include/form_edit') ?>

                </div>

            </div>

        </div>

    </div>

    <div class="row">

        <div class="col-md-12">

            <div class="portlet light bordered">

                <h3>Documentos</h3>

                <form  action="<?= site_url('plantillas/uploadFile/'.$getRow->getId()) ?>" role="form" method="post">

                    <input style="display:none;" name="upload_file" id="upload-file" type="file">

                    <div class="form-body">

                        <div class="form-group">

                            <button cmd="upload_file" name="submit-file" class="btn green open-file" type="button">
                                <i class="fa fa-file-o" aria-hidden="true"></i> Adjuntar documento
                            </button>

                        </div>

                    <div>

                </form>

                <?php if($getAttachments ): ?>

                    <?= $this->load->view('include/files_table') ?>

                <?php else: ?>

                    <div class="alert alert-warning" role="alert">Esta sección no tiene datos para mostrar.</div>

                <?php endif ?>

                

            </div>

        </div>

    </div>

</div>
