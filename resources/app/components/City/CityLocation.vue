<template>
	<section class="city-location">
		<div v-if="inertiaPage.flash.message" class="city-location-message" role="alert" v-html="inertiaPage.flash.message"></div>

		<header class="city-location-heading">
			<div class="city-location-emblem"><GameIcon name="city" /></div>
			<div>
				<h1>{{ title }}</h1>
				<p v-if="description" class="city-location-intro">{{ description }}</p>
			</div>
		</header>

		<div class="city-location-layout">
			<figure class="city-location-map">
				<div class="city-location-scene" :style="{ aspectRatio: map.width + ' / ' + map.height }">
					<img class="city-location-background" :src="imagePath + map.image" :alt="map.alt" :width="map.width" :height="map.height" />
					<MovementLink
						v-for="place in mapPlaces"
						:key="place.location"
						:to="place.location"
						class="city-location-building"
						:class="{
							'is-highlighted': activeLocation === place.location,
							'city-location-hotspot': !place.image,
							'city-location-map-exit': place.exit,
							'city-location-map-exit-right': place.exit && place.direction === 'right',
						}"
						:style="mapPosition(place)"
						:title="place.title"
						@mouseenter="hoveredLocation = place.location"
						@mouseleave="hoveredLocation = null"
						@focus="focusedLocation = place.location"
						@blur="focusedLocation = null"
					>
						<img v-if="place.image" :src="imagePath + place.image" alt="" :width="place.width" :height="place.height" />
						<span v-if="place.number" class="city-location-map-number">{{ place.number }}</span>
					</MovementLink>
					<img
						v-for="decoration in decorations"
						:key="decoration.image"
						class="city-location-decoration"
						:src="imagePath + decoration.image"
						:alt="decoration.title"
						:title="decoration.title"
						:width="decoration.width"
						:height="decoration.height"
						:style="mapPosition(decoration)"
					/>
				</div>
				<figcaption class="city-location-map-caption">
					<GameIcon :name="activePlace ? 'pin' : 'city'" />
					<span v-if="activePlace">
						<strong>{{ activePlace.title }}</strong>
						<span v-if="activePlace.description">· {{ activePlace.description }}</span>
					</span>
					<span v-else>Выберите здание на карте или в списке мест</span>
				</figcaption>
			</figure>

			<nav class="city-location-destinations">
				<div class="city-location-section-heading">
					<h2>{{ placesTitle }}</h2>
				</div>
				<MovementLink
					v-for="place in places"
					:key="place.location"
					:to="place.location"
					class="city-location-place"
					:class="{ 'is-highlighted': activeLocation === place.location }"
					@mouseenter="hoveredLocation = place.location"
					@mouseleave="hoveredLocation = null"
					@focus="focusedLocation = place.location"
					@blur="focusedLocation = null"
				>
					<span class="city-location-place-copy">
						<strong>{{ place.title }}</strong>
						<span v-if="place.description">{{ place.description }}</span>
					</span>
					<span v-if="place.number" class="city-location-place-number">{{ place.number }}</span>
				</MovementLink>
			</nav>
		</div>

		<nav v-if="exits.length" class="city-location-travel">
			<MovementLink v-for="exit in exits" :key="exit.location" :to="exit.location" class="city-location-route">
				<span v-if="exit.direction === 'left'" class="city-location-route-arrow">{{ arrows[exit.direction] }}</span>
				<span class="city-location-route-copy">
					<small>{{ exit.subtitle || 'Соседний район' }}</small>
					<strong>{{ exit.title }}</strong>
				</span>
				<span v-if="exit.direction !== 'left'" class="city-location-route-arrow">{{ arrows[exit.direction] }}</span>
			</MovementLink>
		</nav>
	</section>
</template>

<script setup>
	import MovementLink from '~/components/City/MovementLink.vue';
	import { computed, ref } from 'vue';
	import { usePage } from '@inertiajs/vue3';
	import GameIcon from '~/components/Layout/GameIcon.vue';

	const props = defineProps({
		title: { type: String, required: true },
		description: { type: String, default: '' },
		imagePath: { type: String, required: true },
		map: { type: Object, required: true },
		places: { type: Array, required: true },
		placesTitle: { type: String, default: 'Места поблизости' },
		exits: { type: Array, default: () => [] },
		decorations: { type: Array, default: () => [] },
	});

	const inertiaPage = usePage();
	const hoveredLocation = ref(null);
	const focusedLocation = ref(null);
	const activeLocation = computed(() => hoveredLocation.value ?? focusedLocation.value);
	const mapPlaces = computed(() => [...props.places, ...props.exits.map(exit => ({ ...exit, exit: true }))]);
	const activePlace = computed(() => mapPlaces.value.find(place => place.location === activeLocation.value));
	const arrows = { left: '←', right: '→', up: '↑', down: '↓' };

	function mapPosition(place) {
		return {
			left: (place.x / props.map.width) * 100 + '%',
			top: (place.y / props.map.height) * 100 + '%',
			width: (place.width / props.map.width) * 100 + '%',
			height: (place.height / props.map.height) * 100 + '%',
		};
	}
</script>
