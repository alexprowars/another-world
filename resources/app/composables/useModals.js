import { closeAllModals, openModal } from '@kolirt/vue-modal';
import { markRaw } from 'vue';
import Modal from '~/components/Dialogs/Modal.vue';
import ConfirmPopup from '~/components/Dialogs/Confirm.vue';
import { stopLoading } from '~/composables/useLoading.js';
import i18n from '~/i18n.js';

const openDialog = (component, attrs = {}, options = {}) => {
	const { title = '', ...componentAttrs } = attrs;

	return openModal(Modal, {
		group: 'default',
		props: {
			component: markRaw(component),
			componentAttrs,
			title: title.replace(/<[^>]*>/g, ''),
			...options,
		},
	}).catch(() => null);
};

export const openPopupModal = (component, attrs = {}, options = {}) => {
	return openDialog(component, attrs, options);
};

export const openConfirmModal = (title, content, buttons = [], options = {}) => {
	return openDialog(
		ConfirmPopup,
		{ title, content, buttons },
		{
			contentClass: 'dialog-content dialog-content--confirm',
			persistent: true,
			...options,
		},
	);
};

export const openAlertModal = (title, content) => {
	stopLoading();

	return openDialog(
		ConfirmPopup,
		{ title, content },
		{
			contentClass: 'dialog-content dialog-content--confirm',
		},
	);
};

export const openErrorModal = e => {
	return openAlertModal(i18n.global.t('forms.error'), e.message);
};

export const closeModals = async () => {
	await closeAllModals({ ignoreGuard: true });
};
