export function getUnmetItemRequirements(item, player) {
	return Object.entries(item.requirements || {}).filter(([key, value]) => {
		if (!value) return false;

		return key === 'profession' ? player[key] !== value : (player[key] ?? 0) < value;
	});
}
