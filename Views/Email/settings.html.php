<?php

$alias = $form->vars['name'];
?>


<div class="row">
    <div class="form-group col-xs-12 ">
        <label class="control-label" for="<?php echo $alias; ?>_toAddress" data-toggle="tooltip" data-container="body"
               data-placement="top" title="<?php echo $view['translator']->trans('mautic.email.to_address.tooltip'); ?>"><?php echo $view['translator']->trans('mautic.email.to_address'); ?> <i
                class="fa fa-question-circle"></i></label>
        <div class="input-group">
                    <span class="input-group-addon preaddon">
        <i class="fa fa-envelope"></i>
    </span>
            <input autocomplete="false" type="text"
                   id="<?php echo $alias; ?>_toAddress" name="toAddress" value="<?php echo $toAddress; ?>" class="form-control"
                   autocomplete="false"/>

        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 ">
        <label class="control-label" for="<?php echo $alias; ?>_ccAddress" data-toggle="tooltip" data-container="body"
               data-placement="top" title="<?php echo $view['translator']->trans('mautic.email.cc_address.tooltip'); ?>"><?php echo $view['translator']->trans('mautic.email.cc_address'); ?> <i
                class="fa fa-question-circle"></i></label>
        <div class="input-group">
                    <span class="input-group-addon preaddon">
        <i class="fa fa-envelope"></i>
    </span>
            <input autocomplete="false" type="text"
                   id="<?php echo $alias; ?>_ccAddress" name="ccAddress" value="<?php echo $ccAddress; ?>" class="form-control"
                   autocomplete="false"/>

        </div>
    </div>
</div>


