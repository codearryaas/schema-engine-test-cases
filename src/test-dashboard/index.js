/**
 * Test Dashboard Entry Point
 */

import { render } from '@wordpress/element';
import TestDashboard from './TestDashboard';

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('schema-engine-test-dashboard');
    
    if (container) {
        render(<TestDashboard />, container);
    }
});
