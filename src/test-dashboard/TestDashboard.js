/**
 * Test Dashboard Component
 * UI for running and viewing test results
 */

import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Button, Card, CardBody, Spinner, Notice } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import './style.scss';

const TestDashboard = () => {
    const [tests, setTests] = useState([]);
    const [running, setRunning] = useState(false);
    const [results, setResults] = useState(null);
    const [stats, setStats] = useState(null);
    const [error, setError] = useState(null);
    const [filter, setFilter] = useState('');

    useEffect(() => {
        loadTests();
        loadStats();
    }, []);

    const loadTests = async () => {
        try {
            const response = await apiFetch({
                path: '/schema-engine/v1/tests/list',
            });
            setTests(response.tests || []);
        } catch (err) {
            setError(err.message);
        }
    };

    const loadStats = async () => {
        try {
            const response = await apiFetch({
                path: '/schema-engine/v1/tests/stats',
            });
            setStats(response);
        } catch (err) {
            console.error('Failed to load stats:', err);
        }
    };

    const runTests = async () => {
        setRunning(true);
        setError(null);
        setResults(null);

        try {
            const response = await apiFetch({
                path: '/schema-engine/v1/tests/run',
                method: 'POST',
                data: { filter },
            });

            setResults(response);
            loadStats(); // Reload stats after test run
        } catch (err) {
            setError(err.message);
        } finally {
            setRunning(false);
        }
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'passed':
                return '#00a32a';
            case 'failed':
                return '#d63638';
            case 'error':
                return '#f0b849';
            default:
                return '#50575e';
        }
    };

    const calculatePassRate = () => {
        if (!results || !results.tests) return 0;
        const passed = results.tests - results.failures - results.errors;
        return ((passed / results.tests) * 100).toFixed(1);
    };

    return (
        <div className="schema-engine-test-dashboard">
            <div className="test-dashboard-header">
                <h1>{__('Test Dashboard', 'schema-engine')}</h1>
                <p className="description">
                    {__('Run and monitor automated tests for Schema Engine', 'schema-engine')}
                </p>
            </div>

            {error && (
                <Notice status="error" isDismissible={false}>
                    {error}
                </Notice>
            )}

            {/* Stats Cards */}
            {stats && (
                <div className="test-stats-grid">
                    <Card>
                        <CardBody>
                            <div className="stat-card">
                                <div className="stat-icon">📊</div>
                                <div className="stat-content">
                                    <div className="stat-label">{__('Total Runs', 'schema-engine')}</div>
                                    <div className="stat-value">{stats.total_runs}</div>
                                </div>
                            </div>
                        </CardBody>
                    </Card>

                    <Card>
                        <CardBody>
                            <div className="stat-card">
                                <div className="stat-icon">✅</div>
                                <div className="stat-content">
                                    <div className="stat-label">{__('Success Rate', 'schema-engine')}</div>
                                    <div className="stat-value">{stats.success_rate.toFixed(1)}%</div>
                                </div>
                            </div>
                        </CardBody>
                    </Card>

                    <Card>
                        <CardBody>
                            <div className="stat-card">
                                <div className="stat-icon">⚡</div>
                                <div className="stat-content">
                                    <div className="stat-label">{__('Avg Time', 'schema-engine')}</div>
                                    <div className="stat-value">{stats.average_time.toFixed(2)}s</div>
                                </div>
                            </div>
                        </CardBody>
                    </Card>

                    <Card>
                        <CardBody>
                            <div className="stat-card">
                                <div className="stat-icon">🧪</div>
                                <div className="stat-content">
                                    <div className="stat-label">{__('Test Files', 'schema-engine')}</div>
                                    <div className="stat-value">{tests.length}</div>
                                </div>
                            </div>
                        </CardBody>
                    </Card>
                </div>
            )}

            {/* Test Controls */}
            <Card>
                <CardBody>
                    <div className="test-controls">
                        <div className="control-group">
                            <label htmlFor="test-filter">
                                {__('Filter Tests', 'schema-engine')}
                            </label>
                            <input
                                id="test-filter"
                                type="text"
                                className="regular-text"
                                value={filter}
                                onChange={(e) => setFilter(e.target.value)}
                                placeholder={__('e.g., Organization, Article', 'schema-engine')}
                                disabled={running}
                            />
                        </div>

                        <Button
                            variant="primary"
                            onClick={runTests}
                            disabled={running}
                            style={{ minWidth: '150px' }}
                        >
                            {running ? (
                                <>
                                    <Spinner />
                                    {__('Running...', 'schema-engine')}
                                </>
                            ) : (
                                <>🚀 {__('Run Tests', 'schema-engine')}</>
                            )}
                        </Button>
                    </div>
                </CardBody>
            </Card>

            {/* Test Results */}
            {results && (
                <Card>
                    <CardBody>
                        <div className="test-results">
                            <div className="results-header">
                                <h2>{__('Test Results', 'schema-engine')}</h2>
                                <div className={`results-badge ${results.success ? 'success' : 'failure'}`}>
                                    {results.success ? __('✓ All Tests Passed', 'schema-engine') : __('✗ Some Tests Failed', 'schema-engine')}
                                </div>
                            </div>

                            {/* Summary Stats */}
                            <div className="results-summary">
                                <div className="summary-item">
                                    <span className="label">{__('Tests:', 'schema-engine')}</span>
                                    <span className="value">{results.tests}</span>
                                </div>
                                <div className="summary-item">
                                    <span className="label">{__('Assertions:', 'schema-engine')}</span>
                                    <span className="value">{results.assertions}</span>
                                </div>
                                <div className="summary-item">
                                    <span className="label">{__('Failures:', 'schema-engine')}</span>
                                    <span className="value error">{results.failures}</span>
                                </div>
                                <div className="summary-item">
                                    <span className="label">{__('Errors:', 'schema-engine')}</span>
                                    <span className="value error">{results.errors}</span>
                                </div>
                                <div className="summary-item">
                                    <span className="label">{__('Time:', 'schema-engine')}</span>
                                    <span className="value">{results.time}s</span>
                                </div>
                                <div className="summary-item">
                                    <span className="label">{__('Pass Rate:', 'schema-engine')}</span>
                                    <span className="value success">{calculatePassRate()}%</span>
                                </div>
                            </div>

                            {/* Failure Details */}
                            {!results.success && results.failure_details && (
                                <div className="failure-details">
                                    <h3>{__('Failure Details', 'schema-engine')}</h3>
                                    <pre className="error-log">{results.failure_details}</pre>
                                </div>
                            )}

                            {/* Individual Test Results */}
                            {results.test_results && results.test_results.length > 0 && (
                                <div className="test-list">
                                    <h3>{__('Individual Tests', 'schema-engine')}</h3>
                                    <div className="test-items">
                                        {results.test_results.map((test, index) => (
                                            <div
                                                key={index}
                                                className={`test-item ${test.status}`}
                                                style={{ borderLeftColor: getStatusColor(test.status) }}
                                            >
                                                <span className="test-status-icon">
                                                    {test.status === 'passed' ? '✓' : '✗'}
                                                </span>
                                                <span className="test-name">{test.name}</span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* Raw Output (Collapsible) */}
                            <details className="test-output">
                                <summary>{__('View Raw Output', 'schema-engine')}</summary>
                                <pre>{results.output}</pre>
                            </details>
                        </div>
                    </CardBody>
                </Card>
            )}

            {/* Available Tests */}
            <Card>
                <CardBody>
                    <h2>{__('Available Test Suites', 'schema-engine')}</h2>
                    <p className="description">
                        {__('Run individual test suites or filter by category', 'schema-engine')}
                    </p>

                    {/* Group tests by category */}
                    {Object.entries(
                        tests.reduce((groups, test) => {
                            const category = test.category || 'Other';
                            if (!groups[category]) groups[category] = [];
                            groups[category].push(test);
                            return groups;
                        }, {})
                    ).map(([category, categoryTests]) => (
                        <div key={category} className="test-category">
                            <h3 className="category-title">
                                <span className="category-icon">
                                    {category === 'Schema Types' && '📋'}
                                    {category === 'Template Conditions' && '🎯'}
                                    {category === 'Settings & Admin' && '⚙️'}
                                </span>
                                {category}
                                <span className="category-count">({categoryTests.length})</span>
                            </h3>
                            <div className="test-suites">
                                {categoryTests.map((test, index) => (
                                    <div key={index} className="test-suite-item">
                                        <div className="suite-icon">
                                            {test.plugin === 'pro' ? '💎' : '🧪'}
                                        </div>
                                        <div className="suite-content">
                                            <div className="suite-header">
                                                <span className="suite-name">{test.name}</span>
                                                <span className={`suite-badge ${test.plugin}`}>
                                                    {test.plugin === 'pro' ? 'PRO' : 'FREE'}
                                                </span>
                                            </div>
                                            <div className="suite-file">{test.file}</div>
                                        </div>
                                        <Button
                                            variant="secondary"
                                            size="small"
                                            onClick={() => {
                                                setFilter(test.name);
                                                setTimeout(() => runTests(), 100);
                                            }}
                                            disabled={running}
                                        >
                                            {__('Run', 'schema-engine')}
                                        </Button>
                                    </div>
                                ))}
                            </div>
                        </div>
                    ))}
                </CardBody>
            </Card>
        </div>
    );
};

export default TestDashboard;
