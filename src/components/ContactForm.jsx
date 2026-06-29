import React, { useState } from 'react';
import './ContactForm.css';

export default function ContactForm() {
    const [name, setName] = useState('');
    const [contact, setContact] = useState('');
    const [interest, setInterest] = useState('General Query');
    const [message, setMessage] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [success, setSuccess] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!name || !contact || !message) {
            setError('Please fill in all required fields.');
            return;
        }

        setLoading(true);
        setError(null);
        setSuccess(false);

        const formData = new FormData();
        formData.append('action', 'dascentist_submit_contact');
        formData.append('name', name.trim());
        formData.append('contact', contact.trim());
        formData.append('interest', interest);
        formData.append('message', message.trim());

        try {
            const res = await fetch(daScientistGlobals.ajax_url, {
                method: 'POST',
                body: formData,
            });
            const data = await res.json();
            if (data.success) {
                setSuccess(true);
                setName('');
                setContact('');
                setMessage('');
                setInterest('General Query');
            } else {
                setError(data.data?.message || 'Error sending message. Please try again.');
            }
        } catch (err) {
            setError('Connection error. Please check your network.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="luxury-contact-container">
            <div className="contact-grid">
                
                {/* Left: Contact Info */}
                <div className="contact-info-panel">
                    <span className="contact-eyebrow">Client Services</span>
                    <h2>Connect with the House</h2>
                    <p className="contact-description">
                        Have queries regarding batch formulations, custom oil concentrations, or private orders? 
                        Our Lahore-based compounding team and client services are available to assist you.
                    </p>

                    <div className="contact-details-list">
                        <div className="detail-item">
                            <span className="detail-label">Client Relations (WhatsApp)</span>
                            <span className="detail-val">+92 300 0000000</span>
                        </div>
                        <div className="detail-item">
                            <span className="detail-label">Direct Correspondence</span>
                            <span className="detail-val">concierge@dascentist.com</span>
                        </div>
                        <div className="detail-item">
                            <span className="detail-label">Laboratory Dispatch</span>
                            <span className="detail-val">Lahore, Punjab, Pakistan</span>
                        </div>
                    </div>
                </div>

                {/* Right: Contact Form */}
                <div className="contact-form-panel">
                    {success ? (
                        <div className="contact-success-screen">
                            <span className="success-icon">✻</span>
                            <h3>Message Dispatched</h3>
                            <p>Thank you for your correspondence. A client relations specialist from the house will contact you within 24 hours.</p>
                            <button onClick={() => setSuccess(false)} className="success-reset-btn">
                                Send Another Message
                            </button>
                        </div>
                    ) : (
                        <form className="contact-form" onSubmit={handleSubmit}>
                            <div className="contact-field-group">
                                <label htmlFor="contact-name">Name / House Name</label>
                                <input
                                    type="text"
                                    id="contact-name"
                                    placeholder="Your Name"
                                    value={name}
                                    onChange={(e) => setName(e.target.value)}
                                    required
                                />
                            </div>

                            <div className="contact-field-group">
                                <label htmlFor="contact-phone-email">Contact Phone or Email</label>
                                <input
                                    type="text"
                                    id="contact-phone-email"
                                    placeholder="e.g. 0300-1234567 or mail@domain.com"
                                    value={contact}
                                    onChange={(e) => setContact(e.target.value)}
                                    required
                                />
                            </div>

                            <div className="contact-field-group">
                                <label htmlFor="contact-interest">Subject of Interest</label>
                                <select
                                    id="contact-interest"
                                    value={interest}
                                    onChange={(e) => setInterest(e.target.value)}
                                >
                                    <option value="General Query">General Query</option>
                                    <option value="Compounding Query">Batch Compounding / Concentrations</option>
                                    <option value="Bulk Order">Custom Private Gift Orders</option>
                                    <option value="Order Dispute">Order Status / Delivery Query</option>
                                </select>
                            </div>

                            <div className="contact-field-group">
                                <label htmlFor="contact-message">Message / Confidential Request</label>
                                <textarea
                                    id="contact-message"
                                    placeholder="Write your correspondence here..."
                                    rows="4"
                                    value={message}
                                    onChange={(e) => setMessage(e.target.value)}
                                    required
                                ></textarea>
                            </div>

                            {error && <div className="contact-error-notice">{error}</div>}

                            <button type="submit" className="contact-submit-btn" disabled={loading}>
                                {loading ? 'Dispatching...' : 'Submit Correspondence'}
                            </button>
                        </form>
                    )}
                </div>

            </div>
        </div>
    );
}
