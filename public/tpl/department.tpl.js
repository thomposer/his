define(function () {
    return ''
            + '<div class="col-sm-4">'
            + ''
            + '<div class="form-group field-user-clinic_id required">'
            + ''
            + '<label class="control-label" for="user-clinic_id">所属诊所<span class="label-required">*</span></label>'
            + ''
            + '<select id="user-clinic_id" class="form-control user-clinic_id" name="User[clinic_id][]" autocomplete="off">'
            + ''
            + '<option value="">请选择</option>'
            + ''
            + '{{each department}}'
            + ''
            + '<option value="{{$value.id}}">{{$value.spot_name}}</option>'
            + ''
            + '{{/each}}'
            + ''
            + '</select>'
            + ''
            + ''
            + ''
            + '<div class="help-block"></div>'
            + ''
            + '</div>    </div>  '
            + ''
            + '<div class="col-sm-4">'
            + ''
            + '<div class="form-group field-user-department">'
            + ''
            + '<label class="control-label" for="user-department">科室</label>'
            + ''
            + '<select id="user-department" class="form-control user-department_id" name="User[department][]" autocomplete="off">'
            + ''
            + '<option value="">请选择</option>'
            + ''
            + '</select>'
            + ''
            + ''
            + ''
            + '<div class="help-block"></div>'
            + ''
            + '</div>    </div> '
            + ''
            + '<div class="col-sm-4">'
            + ''
            + '<div class="form-group">'
            + ''
            + '<a href="javascript:void(0);" class="btn-from-delete-add btn clinic-delete">'
            + ''
            + '<i class="fa fa-minus"></i>'
            + ''
            + '</a>'
            + ''
            + '<a href="javascript:void(0);" class="btn-from-delete-add btn clinic-add">'
            + ''
            + '<i class="fa fa-plus"></i>'
            + ''
            + '</a>'
            + ''
            + '</div>'
            + ''
            + '</div>';
});