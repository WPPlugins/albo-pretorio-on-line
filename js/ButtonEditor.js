(function() {

    tinymce.create('tinymce.plugins.albo', {

        init : function(ed, url) {


			var elem = url.split("/");

  			var str = "";

  			for (var i = 0; i < elem.length-1; i++)

    			str += elem[i] + "/";

			ed.addCommand('frmAlbo', function() {

				ed.windowManager.open({

					file : url + '/gencode.php',

					width : 300, 

					height : 170,

					inline : 1

				})

			});

             ed.addButton('albo', {

                title : 'Albo Pretorio',

                image : str+'img/albo.png',

				cmd   : 'frmAlbo'

            });

        },

        createControl : function(n, cm) {

            return null;

        },

    });

    tinymce.PluginManager.add('albo', tinymce.plugins.albo);

})();