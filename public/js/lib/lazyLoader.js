require.async = function (modules, callback) {
    var _get = function () {
        var fileNames = Array.prototype.slice.call(arguments);
        var dfd = $.Deferred();
        require(fileNames, function () {
            dfd.resolve.apply(dfd, arguments);
        });
        return dfd.promise();
    };
    var eval_var = '',
    eval_arguments = [];
    _.each(modules, function (val, key) {
        eval_arguments.push('obj_' + key);
        eval_var += 'var obj_' + key + '=_get("' + val + '");';
    });
    eval(eval_var + '$.when(' + eval_arguments + ').then(function (' + eval_arguments + ') {\
        callback(' + eval_arguments + ');\
    });');
};