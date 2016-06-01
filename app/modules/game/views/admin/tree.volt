<link rel="stylesheet" type="text/css" href="/assets/global/plugins/jstree/dist/themes/default/style.min.css"/>
<script type="text/javascript" src="/assets/global/plugins/jstree/dist/jstree.min.js"></script>

<div class="portlet yellow box">
	<div class="portlet-title">
		<div class="caption">
			Дерево сайта
		</div>
	</div>
	<div class="portlet-body">
		<div id="tree" class="tree-demo"></div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function()
	{
		$("#tree").jstree({
			"core" : {
			   "themes" : {
				   "responsive": false
			   },
			   "check_callback" : true,
			   'data':
			   {
				   	'url' : function (node)
					{
						return '/admin/tree/action/node/';
					},
			       	'data' : function (node)
				   	{
			       		return { 'parent' : node.id };
			       	}
			   }
		   },
		   "types" : {
			   "default" : {
				   "icon" : "fa fa-folder icon-warning icon-lg"
			   },
			   "file" : {
				   "icon" : "fa fa-file icon-warning icon-lg"
			   }
		   },
		   "plugins" : [ "contextmenu", "dnd", "types" ],
			"contextmenu":
			{
				select_node : true,
				show_at_node : true,
				items : function (o) { // Could be an object directly
					return {
						"create" : {
							"separator_before"	: false,
							"separator_after"	: true,
							"_disabled"			: false,
							"label"				: "Добавить",
							"action"			: function (data) {
								var inst = $.jstree.reference(data.reference),
									obj = inst.get_node(data.reference);
								inst.create_node(obj, {}, "last", function (new_node) {
									setTimeout(function () { inst.edit(new_node); },0);
								});
							}
						},
						"rename" : {
							"separator_before"	: false,
							"separator_after"	: false,
							"_disabled"			: false,
							"label"				: "Переименовать",
							"action"			: function (data) {
								var inst = $.jstree.reference(data.reference),
									obj = inst.get_node(data.reference);
								inst.edit(obj);
							}
						},
						"remove" : {
							"separator_before"	: false,
							"icon"				: false,
							"separator_after"	: false,
							"_disabled"			: false,
							"label"				: "Удалить",
							"action"			: function (data) {
								var inst = $.jstree.reference(data.reference),
									obj = inst.get_node(data.reference);
								if(inst.is_selected(obj)) {
									inst.delete_node(inst.get_selected());
								}
								else {
									inst.delete_node(obj);
								}
							}
						}
					};
				}
			}
		});
	});
</script>