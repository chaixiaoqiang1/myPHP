<?php if (!defined('THINK_PATH')) exit();?>
    <div class="row">
        <form class="form-horizontal" method="post" enctype="multipart/form-data" action="<?php echo U('Slide/editor');?>">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="border:1px solid #eeeeee;">
                    <div class="row" style="margin-top: 20px;">
                        <input type="hidden" name='id' value="<?php echo ($data["id"]); ?>"/>
                        <div class="row">
                            <div class="form-group add_pro_list">
                                <label class="col-lg-3 control-label">标题:</label>
                                <div class="col-lg-4">
                                    <input type="text" class="form-control" name="slide_name"  value="<?php echo ($data["slide_name"]); ?>" required/>
                                </div>
                                <span class="col-lg-5 height-center text-danger"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group add_pro_list">
                                <label class="col-lg-3 control-label">连接:</label>
                                <div class="col-lg-4">
                                    <input type="text" class="form-control" value="<?php echo ($data["url"]); ?>" name="url"/>
                                </div>
                                <span class="col-lg-5 height-center text-danger"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group add_pro_list">
                                <label class="col-lg-3 control-label">排序:</label>
                                <div class="col-lg-4">
                                    <input type="number" class="form-control" value="<?php echo ($data["sort"]); ?>" name="sort"/>
                                </div>
                                <span class="col-lg-5 height-center text-danger"></span>
                            </div>
                        </div>
                        <div class="form-group add_pro_list">
                            <label class="col-lg-3 control-label">轮播图片:</label>
                            <div class="col-lg-1">
                                <div class="fileInput left" id="upload-container" >
                                    <input type="file" name="slide_pic" id="upload"  class="upfile uplo" />
                                    <input class="upFileBtn uplo" type="button" value="上传图片" onclick="document.getElementById('upload').click()" />
                                </div>
                            </div>
                            <div class="col-lg-8 height-center text-danger" id="show_img">
                                <img src="/<?php echo ($data["slide_pic"]); ?>" alt="" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group add_pro_list">
                            <div class="col-lg-8 col-lg-offset-3">
                                <input type="submit" class="btn btn-primary"/>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <script src="/taiyou/Public/script/jquery-2.1.1.min.js"></script>
    <script src="/taiyou/Public/script/bootstrap.min.js"></script>