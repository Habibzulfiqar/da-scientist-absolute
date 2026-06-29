import React, { useState } from 'react';
import './OrderTracking.css';

export default function OrderTracking() {
    const [orderId, setOrderId] = useState('');
    const [contact, setContact] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [result, setResult] = useState(null);

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!orderId || !contact) {
            setError('Please enter both Order ID and Billing Email or Phone.');
            return;
        }

        setLoading(true);
        setError(null);
        setResult(null);

        const formData = new FormData();
        formData.append('action', 'dascentist_track_order');
        formData.append('order_id', orderId.trim());
        formData.append('contact', contact.trim());

        try {
            const res = await fetch(daScientistGlobals.ajax_url, {
                method: 'POST',
                body: formData,
            });
            const data = await res.json();
            if (data.success) {
                setResult(data.data);
            } else {
                setError(data.data?.message || 'Error tracing order.');
            }
        } catch (err) {
            setError('Connection error. Please try again.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="luxury-tracking-container">
            <div className="tracking-header">
                <h2>Track Your Scent Order</h2>
                <p>Enter your 4-digit order number and billing details to retrieve live courier progress.</p>
            </div>

            <form className="tracking-form" onSubmit={handleSubmit}>
                <div className="tracking-form-row">
                    <div className="tracking-field-group">
                        <label htmlFor="track-order-id">Order ID / Reference Number</label>
                        <input
                            type="text"
                            id="track-order-id"
                            placeholder="e.g. 2486"
                            value={orderId}
                            onChange={(e) => setOrderId(e.target.value)}
                            required
                        />
                    </div>

                    <div className="tracking-field-group">
                        <label htmlFor="track-contact">Billing Phone or Email</label>
                        <input
                            type="text"
                            id="track-contact"
                            placeholder="e.g. 0300-1234567 or mail@domain.com"
                            value={contact}
                            onChange={(e) => setContact(e.target.value)}
                            required
                        />
                    </div>
                </div>

                <button type="submit" className="tracking-submit-btn" disabled={loading}>
                    {loading ? 'Tracing Order...' : 'Track Order'}
                </button>
            </form>

            {error && <div className="tracking-error-notice">{error}</div>}

            {result && (
                <div className="tracking-result-card">
                    <div className="result-meta-row">
                        <div>
                            <span className="meta-label">Order Ref:</span>
                            <span className="meta-val">#{result.order_id}</span>
                        </div>
                        <div>
                            <span className="meta-label">Date Compounded:</span>
                            <span className="meta-val">{result.date}</span>
                        </div>
                        <div>
                            <span className="meta-label">Total Value:</span>
                            <span className="meta-val">{result.total}</span>
                        </div>
                    </div>

                    {/* Timeline progress bar */}
                    <div className="tracking-timeline">
                        <div className="timeline-progress-bar">
                            <div 
                                className="timeline-progress-fill" 
                                style={{ width: `${(Math.min(result.step, 3) - 1) * 50}%` }}
                            ></div>
                        </div>
                        <div className="timeline-steps">
                            <div className={`timeline-step ${result.step >= 1 ? 'is-active' : ''}`}>
                                <div className="step-bullet">1</div>
                                <span className="step-label">Order Confirmed</span>
                            </div>
                            <div className={`timeline-step ${result.step >= 2 ? 'is-active' : ''}`}>
                                <div className="step-bullet">2</div>
                                <span className="step-label">Compounding</span>
                            </div>
                            <div className={`timeline-step ${result.step >= 3 ? 'is-active' : ''}`}>
                                <div className="step-bullet">3</div>
                                <span className="step-label">Dispatched</span>
                            </div>
                        </div>
                    </div>

                    <div className="result-status-summary">
                        <span className="status-label">Current Stage:</span>
                        <span className="status-badge">{result.status}</span>
                    </div>

                    <div className="result-items-box">
                        <h4>Items in Package:</h4>
                        <ul>
                            {result.items.map((item, idx) => (
                                <li key={idx}>✻ {item}</li>
                            ))}
                        </ul>
                    </div>
                </div>
            )}
        </div>
    );
}
