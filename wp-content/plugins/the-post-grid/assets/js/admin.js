(function($){
    'use strict';
    if($('.rt-color').length) {
        $('.rt-color').wpColorPicker();
    }
    if($('#sc-tabs').length) {
        $('#sc-tabs').tabs();
    }
    if($(".rt-select2").length) {
        $(".rt-select2").select2({dropdownAutoWidth: true});
    }
    var postType = jQuery("#rc-sc-post-type").val();
    rtTgpFilter();
    thpShowHideScMeta();
    $('#post_filter input[type=checkbox]').live('change', function() {
        var id = $(this).val();
        if(id == 'tpg_taxonomy'){
            if(this.checked){
                rtTPGTaxonomyListByPostType(postType, $(this));
            }else{
                jQuery('.rt-tpg-filter.taxonomy > .taxonomy-field').hide('slow').html('');
                jQuery('.rt-tpg-filter.taxonomy > .rt-tpg-filter-item .term-filter-holder').hide('slow').html('');
                jQuery('.rt-tpg-filter.taxonomy > .rt-tpg-filter-item .term-filter-item-relation').hide('slow');
            }
        }
        if(this.checked){
            $(".rt-tpg-filter."+id).show('slow');
        }else{
            $(".rt-tpg-filter."+id).hide('slow');
        }

    });

    $('#post-taxonomy input[type=checkbox]').live('change', function() {
            thpShowHideScMeta();
            rtTPGTermListByTaxonomy( $(this) );
    });

    $("#rt-tpg-pagination").live('change', function() {
        if(this.checked){
            jQuery(".field-holder.posts-per-page").show();
        }else{
            jQuery(".field-holder.posts-per-page").hide();
        }
    });

    $("#rt-tpg-sc-layout").on("change", function (e) {
        thpShowHideScMeta();
    });

    $("#rc-sc-post-type").on("change", function (e) {
        postType = $(this).select2("val");
        if(postType){
            rtTPGIsotopeFilter($(this));
            $('#post_filter input[type=checkbox]').each(function(){
                $(this).prop('checked', false);
            });
            $(".rt-tpg-filter.taxonomy > .taxonomy-field").html('');
            $(".rt-tpg-filter.taxonomy > .rt-tpg-filter-item .term-filter-item-container").remove();
            $(".rt-tpg-filter.hidden").hide();
            $(".field-holder.term-filter-item-relation ").hide();
        }
    });

    $(window).scroll(function() {
        var height = $(window).scrollTop();

        if(height  > 50) {
            $('.post-type-rttpg div#submitdiv').addClass('sticky');
        }else{
            $('.post-type-rttpg div#submitdiv').removeClass('sticky');
        }
    });

})(jQuery);

function rtTPGTaxonomyListByPostType( postType, $this){

    var arg = "post_type="+postType;
    var bindElement = $this;
    tpgAjaxCall( bindElement, 'rtTPGTaxonomyListByPostType', arg, function(data){
        //console.log(data);
        if(data.error){
            alert(data.msg);
        }else{
            jQuery('.rt-tpg-filter.taxonomy > .taxonomy-field').html(data.data).show('slow');
        }
    });
}

function rtTPGIsotopeFilter( $this ) {
    var arg = "post_type="+$this.val();
    var bindElement = $this;
    var target = jQuery('.field-holder.sc-isotope-filter .field > select');
    tpgAjaxCall( bindElement, 'rtTPGIsotopeFilter', arg, function(data){
        if(data.error){
            alert(data.msg);
        }else{
            target.html(data.data);
            tgpLiveReloadScript();
        }
    });
}

function rtTPGTermListByTaxonomy( $this ){
    var term = $this.val();
    var targetHolder = jQuery('.rt-tpg-filter.taxonomy').children('.rt-tpg-filter-item').children('.field-holder').children('.term-filter-holder');
    var target = targetHolder.children('.term-filter-item-container.'+term);
    if($this.is(':checked')){
        var arg = "taxonomy="+$this.val();
        var bindElement = $this;
        tpgAjaxCall( bindElement, 'rtTPGTermListByTaxonomy', arg, function(data){
            //console.log(data);
            if(data.error){
                alert(data.msg);
            }else{
                targetHolder.show();
                jQuery(data.data).prependTo(targetHolder).fadeIn('slow');
                tgpLiveReloadScript();
            }
        });
    }else{
        target.hide('slow').html('').remove();
    }

    var termLength = jQuery('input[name="tpg_taxonomy[]"]:checked').length;
    if(termLength > 1){
        jQuery('.field-holder.term-filter-item-relation ').show('slow');
    }else{
        jQuery('.field-holder.term-filter-item-relation ').hide('slow');
    }

}

( function( global, $ ) {
    var editor,
        syncCSS = function() {
            thpSyncCss();
        },
        loadAce = function() {
            $('.rt-custom-css').each(function(){
                var id = $(this).find('.custom-css').attr('id');
                editor = ace.edit( id );
                global.safecss_editor = editor;
                editor.getSession().setUseWrapMode( true );
                editor.setShowPrintMargin( false );
                editor.getSession().setValue( $(this).find('.custom_css_textarea').val() );
                editor.getSession().setMode( "ace/mode/css" );
            });

            jQuery.fn.spin&&$( '.custom_css_container' ).spin( false );
            $( '#post' ).submit( syncCSS );
        };
    if ( $.browser.msie&&parseInt( $.browser.version, 10 ) <= 7 ) {
        $( '.custom_css_container' ).hide();
        $( '.custom_css_textarea' ).show();
        return false;
    } else {
        $( global ).load( loadAce );
    }
    global.aceSyncCSS = syncCSS;
} )( this, jQuery );

function thpSyncCss(){
    jQuery('.rt-custom-css').each(function(){
        var e = ace.edit( jQuery(this).find('.custom-css').attr('id') );
        jQuery(this).find('.custom_css_textarea').val( e.getSession().getValue() );
    });
}
function rtTPGSettings(e){
    thpSyncCss();
    jQuery('rt-response').hide();
    var arg = jQuery( e ).serialize();
    var bindElement = jQuery('.rtSaveButton');
    tpgAjaxCall( bindElement, 'rtTPGSettings', arg, function(data){
        if(data.error){
            jQuery('.rt-response').addClass('updated');
            jQuery('.rt-response').removeClass('error');
            jQuery('.rt-response').show('slow').text(data.msg);
        }else{
            jQuery('.rt-response').addClass('error');
            jQuery('.rt-response').show('slow').text(data.msg);
        }
    });

}


function tpgAjaxCall( element, action, arg, handle){
    var data;
    if(action) data = "action=" + action;
    if(arg)    data = arg + "&action=" + action;
    if(arg && !action) data = arg;

    var n = data.search(rttpg.nonceID);
    if(n<0){
        data = data + "&rttpg_nonce=" + rttpg.nonce;
    }
    jQuery.ajax({
        type: "post",
        url: rttpg.ajaxurl,
        data: data,
        beforeSend: function() { jQuery("<span class='rt-loading'></span>").insertAfter(element); },
        success: function( data ){
            jQuery(".rt-loading").remove();
            handle(data);
        }
    });
}

function rtTgpFilter(){
    jQuery("#post_filter input[type=checkbox]:checked").each(function(){
       var id = jQuery(this).val();
        jQuery(".rt-tpg-filter."+id).show();
    });

    jQuery("#post-taxonomy input[type=checkbox]:checked").each(function(){
       var id = jQuery(this).val();
        jQuery(".filter-item."+id).show();
    });

}

function thpShowHideScMeta(){

    if(jQuery("#rt-tpg-sc-layout").val() == 'isotope1'){
        jQuery(".field-holder.pagination, .field-holder.posts-per-page").hide();
        jQuery(".field-holder.sc-isotope-filter").show();
    }else{
        jQuery(".field-holder.pagination").show();
        jQuery(".field-holder.sc-isotope-filter").hide();
        var pagination = jQuery("#rt-tpg-pagination").is(':checked');
        if(pagination){
            jQuery(".field-holder.posts-per-page").show();
        }else{
            jQuery(".field-holder.posts-per-page").hide();
        }
    }

    if(jQuery("#post-taxonomy input[name='tpg_taxonomy[]']").is(":checked")){
        jQuery(".rt-tpg-filter-item.term-filter-item").show();
    }else{
        jQuery(".rt-tpg-filter-item.term-filter-item").hide();
    }

}

function tgpLiveReloadScript(){
    jQuery("select.rt-select2").select2({ dropdownAutoWidth : true });
}