import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    darkMode: 'class', // Utiliser la classe 'dark' pour le mode sombre

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                // Couleurs personnalisées pour Kanboard
                kanboard: {
                    blue: {
                        50: '#eff6ff',
                        100: '#dbeafe',
                        200: '#bfdbfe',
                        300: '#93c5fd',
                        400: '#60a5fa',
                        500: '#3b82f6',
                        600: '#2563eb',
                        700: '#1d4ed8',
                        800: '#1e40af',
                        900: '#1e3a8a',
                    },
                    gray: {
                        50: '#f9fafb',
                        100: '#f3f4f6',
                        200: '#e5e7eb',
                        300: '#d1d5db',
                        400: '#9ca3af',
                        500: '#6b7280',
                        600: '#4b5563',
                        650: '#404853', // Couleur personnalisée pour les cards dark
                        700: '#374151',
                        800: '#1f2937',
                        900: '#111827',
                    }
                }
            },

            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
            },

            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'slide-up': 'slideUp 0.6s ease-out',
                'slide-down': 'slideDown 0.6s ease-out',
                'scale-in': 'scaleIn 0.3s ease-out',
                'bounce-subtle': 'bounceSubtle 0.6s ease-in-out',
                'pulse-soft': 'pulseSoft 2s infinite',
                'task-enter': 'taskEnter 0.3s ease-out',
                'task-update': 'taskUpdate 0.5s ease-in-out',
                'task-remove': 'taskRemove 0.3s ease-in-out',
            },

            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': {
                        opacity: '0',
                        transform: 'translateY(20px)'
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'translateY(0)'
                    },
                },
                slideDown: {
                    '0%': {
                        opacity: '0',
                        transform: 'translateY(-20px)'
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'translateY(0)'
                    },
                },
                scaleIn: {
                    '0%': {
                        opacity: '0',
                        transform: 'scale(0.9)'
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'scale(1)'
                    },
                },
                bounceSubtle: {
                    '0%, 100%': {
                        transform: 'translateY(0)'
                    },
                    '50%': {
                        transform: 'translateY(-5px)'
                    },
                },
                pulseSoft: {
                    '0%, 100%': {
                        opacity: '1'
                    },
                    '50%': {
                        opacity: '0.7'
                    },
                },
                taskEnter: {
                    '0%': {
                        opacity: '0',
                        transform: 'translateY(-10px) scale(0.95)',
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'translateY(0) scale(1)',
                    },
                },
                taskUpdate: {
                    '0%': {
                        transform: 'scale(1)',
                        backgroundColor: 'transparent',
                    },
                    '50%': {
                        transform: 'scale(1.05)',
                        backgroundColor: 'rgb(59 130 246 / 0.1)',
                    },
                    '100%': {
                        transform: 'scale(1)',
                        backgroundColor: 'transparent',
                    },
                },
                taskRemove: {
                    '0%': {
                        opacity: '1',
                        transform: 'scale(1) rotate(0deg)',
                    },
                    '50%': {
                        opacity: '0.5',
                        transform: 'scale(0.8) rotate(-5deg)',
                    },
                    '100%': {
                        opacity: '0',
                        transform: 'scale(0.6) rotate(-10deg)',
                    },
                },
            },

            boxShadow: {
                'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                'medium': '0 4px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                'large': '0 10px 40px -10px rgba(0, 0, 0, 0.15), 0 20px 25px -5px rgba(0, 0, 0, 0.1)',
                'kanban': '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)',
                'kanban-hover': '0 4px 12px 0 rgba(0, 0, 0, 0.15), 0 2px 4px 0 rgba(0, 0, 0, 0.06)',
                'drag': '0 10px 25px rgba(0, 0, 0, 0.15), 0 20px 40px rgba(0, 0, 0, 0.2)',
            },

            borderRadius: {
                'xl': '0.75rem',
                '2xl': '1rem',
                '3xl': '1.5rem',
            },

            backdropBlur: {
                'xs': '2px',
            },

            scale: {
                '98': '0.98',
                '102': '1.02',
            },

            zIndex: {
                '60': '60',
                '70': '70',
                '80': '80',
                '90': '90',
                '100': '100',
            },

            screens: {
                'xs': '475px',
                '3xl': '1680px',
            },

            maxWidth: {
                '8xl': '88rem',
                '9xl': '96rem',
            },

            minHeight: {
                'screen-safe': 'calc(100vh - 200px)',
            },

            transitionProperty: {
                'width': 'width',
                'spacing': 'margin, padding',
            },

            transitionDuration: {
                '400': '400ms',
                '600': '600ms',
            },

            ringWidth: {
                '3': '3px',
                '6': '6px',
            },

            gridTemplateColumns: {
                'kanban': 'repeat(auto-fit, minmax(300px, 1fr))',
                'kanban-mobile': 'repeat(5, minmax(280px, 300px))',
            },
        },
    },

    plugins: [
        forms,

        // Plugin personnalisé pour les utilitaires Kanboard
        function ({ addUtilities, addComponents, theme }) {
            // Utilitaires pour les états de focus améliorés
            addUtilities({
                '.focus-ring': {
                    '&:focus': {
                        outline: 'none',
                        'box-shadow': `0 0 0 2px ${theme('colors.blue.500')}, 0 0 0 4px ${theme('colors.blue.500')}20`,
                    },
                },
                '.focus-ring-inset': {
                    '&:focus': {
                        outline: 'none',
                        'box-shadow': `inset 0 0 0 2px ${theme('colors.blue.500')}`,
                    },
                },
            });

            // Composants pour les cards Kanban
            addComponents({
                '.kanban-card': {
                    '@apply bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600': {},
                    '@apply p-4 shadow-kanban hover:shadow-kanban-hover transition-all duration-200': {},
                    '@apply cursor-grab hover:-translate-y-1 active:cursor-grabbing': {},
                },
                '.kanban-column-container': {
                    '@apply bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700': {},
                    '@apply shadow-soft flex flex-col min-h-0': {},
                },
                '.btn-kanboard': {
                    '@apply inline-flex items-center px-4 py-2 border border-transparent': {},
                    '@apply text-sm font-medium rounded-lg transition-all duration-200': {},
                    '@apply focus-ring active:scale-98': {},
                },
                '.btn-primary': {
                    '@apply btn-kanboard bg-blue-600 hover:bg-blue-700 text-white': {},
                    '@apply shadow-sm hover:shadow-md': {},
                },
                '.btn-secondary': {
                    '@apply btn-kanboard bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600': {},
                    '@apply text-gray-900 dark:text-gray-100': {},
                },
                '.btn-danger': {
                    '@apply btn-kanboard bg-red-600 hover:bg-red-700 text-white': {},
                    '@apply shadow-sm hover:shadow-md': {},
                },
                '.gradient-bg-primary': {
                    'background': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                },
                '.gradient-bg-secondary': {
                    'background': 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                },
                '.gradient-bg-success': {
                    'background': 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                },
            });

            // Utilitaires pour les scrollbars personnalisées
            addUtilities({
                '.scrollbar-thin': {
                    'scrollbar-width': 'thin',
                },
                '.scrollbar-track-transparent': {
                    'scrollbar-color': 'transparent transparent',
                },
                '.scrollbar-thumb-gray': {
                    'scrollbar-color': `${theme('colors.gray.400')} transparent`,
                },
                '.dark .scrollbar-thumb-gray': {
                    'scrollbar-color': `${theme('colors.gray.600')} transparent`,
                },
            });

            // Utilitaires pour les états de drag & drop
            addUtilities({
                '.drag-ghost': {
                    '@apply opacity-40 bg-blue-50 dark:bg-blue-900/50': {},
                    '@apply border-2 border-dashed border-blue-300 dark:border-blue-600': {},
                },
                '.drag-chosen': {
                    '@apply cursor-grabbing transform scale-105 shadow-drag z-50': {},
                    '@apply ring-2 ring-blue-500 ring-opacity-50': {},
                },
                '.drag-active': {
                    '@apply ring-2 ring-blue-500 ring-opacity-50': {},
                    '@apply bg-blue-50 dark:bg-blue-900/20': {},
                },
            });
        },
    ],
};
