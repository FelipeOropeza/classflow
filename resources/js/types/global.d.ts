import { PageProps } from '@inertiajs/core';
import { RouteFunction } from 'ziggy-js';
import { Auth } from '@/types/auth';

declare global {
    var route: RouteFunction;
    interface Window {
        axios: any;
    }
}

declare module '@inertiajs/core' {
    interface PageProps extends Auth {
        [key: string]: unknown;
    }
}

declare module 'vue' {
    interface ComponentCustomProperties {
        route: RouteFunction;
    }
}

export {};
