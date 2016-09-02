{% if objects|length %}
	<div class="shop-items row">
		{% for object in objects %}
			<div class="col-xs-6 col-xl-3 col-lg-4">
				{{ partial('shared/shop_item', ['object': object, 'type': 1]) }}
			</div>
		{% endfor %}
	</div>
{% else %}
	<div>В данном отделе нет товаров.</div>
{% endif %}