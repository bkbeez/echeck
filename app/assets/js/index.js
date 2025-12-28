/*
 * Prototype
*/
// Number
Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};
/*
 * Table
 * option:{'debug':true, 'log':true }
*/
jQuery.fn.tablefilter = function(option) {
    var itable = this;
    $(itable).find("select[name='limit']").change(function(){
        $(itable).find("form").find("button[type='submit']").click();
    });
    $(itable).find("select[name='page']").change(function(){
        $(itable).find("form").find("button[type='submit']").click();
    });
    $(itable).find(".filter-prev button").click(function(){
        var page = parseInt($(itable).find("select[name='page']").val())-1;
        if(page>0){
            $(itable).find("select[name='page']").val(page);
            if(page==1){
                $(this).attr("class", "btn btn-icon btn-icon-start btn-white");
            }
            if((page<parseInt($(itable).find("input[name='pages']").val()))&&$(itable).find(".filter-next button").hasClass("btn-white")){
                $(itable).find(".filter-next button").attr("class", "btn btn-icon btn-icon-end btn-white btn-primary");
            }
            $(itable).find("form").find("button[type='submit']").click();
        }
    });
    $(itable).find(".filter-next button").click(function(){
        var page = parseInt($(itable).find("select[name='page']").val())+1;
        var total = parseInt($(itable).find("input[name='pages']").val());
        if(page<=total){
            $(itable).find("select[name='page']").val(page);
            if(page==total){
                $(this).removeClass("btn-primary").addClass("btn-white");
            }
            if((page>1)&&$(itable).find(".filter-prev button").hasClass("btn-white")){
                $(itable).find(".filter-prev button").attr("class", "btn btn-icon btn-icon-start btn-white btn-primary");
            }
            $(itable).find("form").find("button[type='submit']").click();
        }
    });
    if(option!=undefined&&option.keyword=="auto"){
        $(itable).find("input[name='keyword']").keyup(function(){
            $(itable).find("form").find("input[name='state']").val(null);
            $(itable).find("form").find("button[type='submit']").click();
        });
    }else{
        $(itable).find("input[name='keyword']").change(function(){
            $(itable).find("form").find("button[type='submit']").click();
        });
    }
    if(option==undefined||option.debug==undefined){
        $(itable).find("form").ajaxForm({
            beforeSubmit: function (formData, jqForm, options) {
                $(itable).find("form").find("button[type='submit']").attr('disabled', true);
                if(formData[0].value=="loading"){
                    runStart();
                }
            },
            success: function(rs) {
                runStop();
                var data = JSON.parse(rs);
                $(itable).find("form").find("input[name='state']").val('loading');
                if(option!=undefined&&option.log!=undefined){
                    console.log(data);
                }
                if( data.login!=undefined&&data.login ){
                    $(itable).find("form").find("button[type='submit']").removeAttr('disabled');
                    document.location='/login';
                }else{
                    if(data.status=='success'){
                        $(itable).find("input[name='pages']").val(data.pages);
                        $(itable).find("table tbody").html(data.htmls);
                        if(data.pagination!=undefined){
                            $(itable).find("select[name='page']").html(data.pagination).change(function(){
                                $(itable).find("form").find("button[type='submit']").click();
                            });
                        }
                        $(itable).find(".filter-display").html(data.text);
                        $(itable).find(".filter-pagination .page-total>span").html(data.display);
                        $(itable).find(".filter-prev button").attr("class", "btn btn-sm btn-soft-ash");
                        $(itable).find(".filter-next button").attr("class", "btn btn-sm btn-soft-ash");
                        if( parseInt(data.page)==1&&parseInt(data.page)<parseInt(data.pages) ){
                            $(itable).find(".filter-next button").attr("class", "btn btn-sm btn-primary");
                        }else if( parseInt(data.page)>1&&parseInt(data.page)<parseInt(data.pages) ){
                            $(itable).find(".filter-prev button").attr("class", "btn btn-sm btn-primary");
                            $(itable).find(".filter-next button").attr("class", "btn btn-sm btn-primary");
                        }else if( parseInt(data.page)>1&&parseInt(data.page)==parseInt(data.pages) ){
                            $(itable).find(".filter-prev button").attr("class", "btn btn-sm btn-primary");
                        }
                        $(itable).find("form").find("button[type='submit']").removeAttr('disabled');
                    }else{
                        $(itable).find("form").find("button[type='submit']").removeAttr('disabled');
                        runNotify(data.status, data.title, data.text);
                        if(data.field_focus!=undefined){
                            $(itable).find("input[name='condition["+data.field_focus+"]']").focus();
                        }else if(data.field_open_focus!=undefined){
                            $(itable).find("input[name='condition["+data.field_open_focus+"]']").focus();
                        }
                    }
                }
            }
        });
        $(itable).find("form").find("button[type='submit']").click();
    }
}

// Image Previewer
function imagePreviewer(url, download){
    //var url = $(self).find('img').attr('src');
    var htmls = '<center>';
            htmls += '<div class="modal-dialog'+((download!=undefined)?' download':'')+'">';
                htmls += '<div class="modal-content">';
                    htmls += '<img src="'+url+'" style="" onerror="this.onerror=null;this.src='+$('head').attr('app-path')+'/app/assets/img/noimage.jpg;"/>';
                    htmls += '<center>';
                        htmls += '<div class="i-save"><a download href="'+((download!=undefined)?url:'javascript:void(0);')+'"><i class="uil uil-cloud-download"></i>&nbsp;&nbsp;Download</a></div>';
                        htmls += '<div class="i-close" onclick="imagePreviewerClose()">&#10005; Close</div>';
                    htmls += '</center>';
                htmls += '</div>';
            htmls += '</div>';
        htmls += '</center>';
    $('#ImagePreviewDialog').html(htmls).modal('show', {backdrop: 'true'});
}
function imagePreviewerClose(){
    $('#ImagePreviewDialog').modal('hide');
}
// Run Start
function runStart(){
    swal({
        'showCloseButton': false,
        'showCancelButton': false,
        'showConfirmButton': false,
        'allowEscapeKey': false,
        'allowOutsideClick': false
    }).then(
        function(){},
        function(dismiss){}
    );
    $(".swal2-modal").addClass("run-start");
}
// Run Stop
function runStop(){
    swal.close();
}
// Run Language
function runLanguage(lang){
    $.ajax({
        url: "/app/language/change.php",
        type : 'POST',
        data: { 'lang':lang },
        dataType: "json",
        beforeSend: function( xhr ) {
            runStart();
        }
    }).done(function(data) {
        if( data.status=='success' ){
            document.location.reload();
        }else{
            runStop();
            swal({
                'type': 'error',
                'title': data.title,
                'html': data.text,
                'showConfirmButton': false,
                'timer': 1500
            }).then(
                function () {},
                function (dismiss) {
                    if (dismiss === 'timer') {
                        swal.close();
                    }
                }
            );
        }
    });
}
// Run Logout
function runLogout(){
    $.ajax({
        url: "/logout/index.php",
        type : 'POST',
        data: { 'ajax':true },
        dataType: "json",
        beforeSend: function( xhr ) {
            runStart();
        }
    }).done(function(data) {
        runStop();
        if( data.status=='success' ){
            document.location='/';
        }else{
            swal({
                'type': 'error',
                'title': data.title,
                'html': data.text,
                'showConfirmButton': false,
                'timer': 1500
            }).then(
                function () {},
                function (dismiss) {
                    if (dismiss === 'timer') {
                        swal.close();
                    }
                }
            );
        }
    });
}