import { usePage } from "@inertiajs/vue3";

export default function usePermissions() {

    const permissions = usePage().props.room.permissions;

    const hasPermissions = (key) => {

        if (permissions === 'all') return true;

        const keys = Array.isArray(key) ? key : [key];

        /**
         * Check if any of the keys exist in the permissions object
         */
        return keys.some(permissionKey => {

            const segments = permissionKey.split('.');

            const value = segments.reduce(
                (acc, segment) => {
                    return acc && acc[segment] !== undefined ? acc[segment] : undefined;
                },
                permissions
            );

            return Boolean(value);
        });
    };

    return { permissions, hasPermissions };
}
