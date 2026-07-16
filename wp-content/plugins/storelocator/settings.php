<style>
    .storelocator-settings-page * {
        border-radius: 0 !important;
        box-sizing: border-box;
    }

    .left-bar h4 {
        width: 100%;
        box-shadow: 0px 1px 2px 0px rgb(0 0 0 / 20%);
    }

    #embed-storelocator-info {
        display: flex;
        justify-content: center;

    }

    #embed-storelocator-info>code {
        display: flex;
        justify-content: center;
        align-items: center;
        width: "fit-content"
    }
</style>
<div class="wrap storelocator-settings-page" style="display:flex;flex-direction:column;gap:20px;">
    <div style="border-bottom:1px solid lightgray;" class="header">
        <a href="https://locatestore.com" class="logo"><img src="<?php echo (STLR_DIR_URL . 'storelocator-logo.png'); ?>" width="150px" alt="Storelocator logo" style="margin:20px 0px;" /></a>

    </div>
    <div style="display:flex;justify-content:space-between;">
        <div class="left-bar" style="width:70%;margin-right:10px">
            <div>

                <?php if (get_option("storelocator_url") && get_option("storelocator_path")) { ?><div style="background-color:#00FF0010;padding:10px 20px;margin-bottom:10px;border:1px solid #00FF00;"><?php echo __("✅ Your Store Locator page is active at "); ?> <code><?php echo esc_html(get_site_url() . "/" . get_option("storelocator_path")); ?></code> <a href="<?php echo esc_url(get_site_url() . "/" . get_option("storelocator_path")); ?>" target="_blank" rel="noreferrer"><?php echo esc_html__("Visit page"); ?></a></div>
                <?php } else { ?>
                    <div style="background-color:#FFA50010;padding:10px 20px;margin-bottom:10px;border:1px solid #FFA500;"><?php echo __("Don't have a Store Locator URL ?<a href='https://locatestore.com/#howto' target='_blank' rel='noreferrer'> Learn how to set one up using our addon</a>") ?></div>
                <?php } ?>
            </div>
            <?php if (count($errors) > 0) { ?>
                <div style="background-color:#ffe5e5;padding:10px 20px;margin-bottom:10px;border:1px solid red;">
                    <h4 style="margin:0;box-shadow:none"><?php echo __('Error ⚠️') ?></h4>

                    <ul>
                        <?php
                        array_map(function ($item) {
                            echo wp_kses("<li>&rarr; " . $item . "</li>", array('code' => array(), 'li' => array()));
                        }, $errors); ?>
                    </ul>
                </div>
            <?php } ?>
            <div style="background-color:white;margin-bottom:10px;">
                <h4 style="margin:0;padding:10px 20px;cursor:pointer;" id="generate-page-item"><?php echo __('Set up a new Store Locator page 🚀'); ?></h4>

                <form action="options.php" method="post" id="generate-page-form" style="display:none;padding:0px 0px 10px 0px; border-top:1px solid lightgray;margin:0px 20px;">

                    <?php
                    settings_fields("storelocator-settings-generate-page");
                    do_settings_sections("storelocator");
                    submit_button("Submit", "primary", "submit");
                    ?>
                </form>
            </div>
            <div style="background-color:white;margin-bottom:10px;">
                <h4 style="margin:0;padding:10px 20px;cursor:pointer;" id="embed-storelocator-item"><?php echo __('Add Store Locator to an existing page / post 📝'); ?></h4>

                <div id="embed-storelocator-form" style="display:none;padding:10px 0px 20px 0px; border-top:1px solid lightgray;margin:0px 20px;">

                    <div><?php echo __('Generate your shortcode and paste it into a page / post to embed your Store Locator.'); ?> </div>
                    <div>
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th style="width:fit-content;">Store Locator URL</th>
                                    <td>
                                        <input type="text" style="width:100%" id="embed-storelocator-input" placeholder="https://locatestore.com/<id>" value="<?php echo esc_attr((get_option('storelocator_url') === false ? '' : get_option('storelocator_url')));  ?>" />

                                    </td>
                                    <td>
                                        <div class="button button-primary" onclick="<?php echo esc_js('storelocatorHandleGetShortcode()'); ?>">Get shortcode</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div id="embed-storelocator-info" style="display:none">
                            <code id="shortcode-container"><span id="shortcode-val"></span></code>
                            <div class="button" onclick="<?php echo esc_js('storelocatorHandleCopyShortcode()'); ?>"><?php echo __('Copy'); ?></div>

                        </div>
                        <div id="copied-message" style="display:none;margin:5px 10px 0px 10px;justify-content:center;align-items:center;"><span><?php echo __('Copied'); ?></span></div>
                        <div id="validation-error-message" style="display:none;color:red;">
                            Invalid Store Locator URL. Please enter a valid URL of the form <code style="color:black;">https://locatestore.com/&lt;id&gt;</code>
                        </div>

                    </div>


                </div>



            </div>



        </div>
        <div class="right-bar" style="width:30%">
            <div style="background-color:white;padding:20px;margin-bottom:10px;">
                <h4 style="margin:0;">Show us some love :)</h4>
                <div>
                    <p>Found Store Locator useful? Rate it 5 stars and leave a nice little comment at wordpress.org. We would appreciate that.</p>
                    <p><a href="<?php echo esc_url("https://wordpress.org/support/plugin/storelocator/reviews/#new-post"); ?>" target="_blank" class="button" style="border-radius:0;">Rate 5 Stars</a></p>
                </div>
            </div>
            <div style="background-color:white;padding:20px;">
                <h4 style="margin:0;"><?php echo __("Let's be friends 👍"); ?></h4>
                <div>
                    <p>
                        <a href="<?php echo esc_url("https://www.youtube.com/c/NoCodeSchool?sub_confirmation=1"); ?>" target="_blank" class="button" style="border-radius:0;"><?php echo __('Subscribe on YouTube'); ?></a>
                        <a href="<?php echo esc_url("https://twitter.com/microdotcompany"); ?>" target="_blank" class="button" style="border-radius:0;"><?php echo __('Follow on Twitter'); ?></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById("generate-page-item").addEventListener("click", function(e) {
        const elem = document.getElementById("generate-page-form");
        elem.style.display = elem.style.display == "none" ? "block" : "none";
        const elemNear = document.getElementById("embed-storelocator-form");
        if (elemNear.style.display == "block") elemNear.style.display = "none";
    })
    document.getElementById("embed-storelocator-item").addEventListener("click", function(e) {
        const elem = document.getElementById("embed-storelocator-form");
        elem.style.display = elem.style.display == "none" ? "block" : "none";
        const elemNear = document.getElementById("generate-page-form");
        if (elemNear.style.display == "block") elemNear.style.display = "none";
    })


    function storelocatorHandleCopyShortcode(e) {
        const el = document.createElement('textarea');
        el.value = document.getElementById("shortcode-container").innerText;
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        document.getElementById("copied-message").style.display = "flex";
        setTimeout(function() {
            document.getElementById("copied-message").style.display = "none";

        }, 1000);

    }

    function storelocatorHandleGetShortcode() {

        const val = document.getElementById("embed-storelocator-input").value;
        const regexPattern = /^https:\/\/locatestore.com\/.{3,}$/i;
        if (regexPattern.test(val)) {
            document.getElementById("shortcode-val").innerText = '[storelocator id="' + val.substring(24) +
                '"]';
            document.getElementById("embed-storelocator-info").style.display = "flex";
            document.getElementById("validation-error-message").style.display = "none";


        } else if (val == '') {

            document.getElementById("embed-storelocator-info").style.display = "none";
            document.getElementById("validation-error-message").style.display = "none";
        } else {
            document.getElementById("embed-storelocator-info").style.display = "none";
            document.getElementById("validation-error-message").style.display = "block";
        }


    }
</script>