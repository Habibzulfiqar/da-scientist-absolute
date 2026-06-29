import React, { useState } from 'react';
import './FAQAccordion.css';

const FAQ_DATA = [
    {
        category: 'Fragrance & Longevity',
        questions: [
            {
                q: 'What is the concentration of Da Scientist fragrances?',
                a: 'All our fragrances are compounded as Extrait de Parfum (featuring 30% to 40% pure oil concentration). This is the highest luxury concentration class available in perfumery, far exceeding standard Eau de Parfum levels.'
            },
            {
                q: 'How long do the scents typically last?',
                a: 'Due to the Extrait de Parfum concentration and selection of heavy base notes (ouds, musks, ambers, woods), our fragrances typically project for 8 to 12+ hours on skin, and remain noticeable on clothing fabrics for multiple days.'
            },
            {
                q: 'Are these scents unisex?',
                a: 'Yes. We compound scent profiles based on the character of ingredients rather than traditional gender classifications. Each composition is designed to adapt uniquely to the skin temperature of the wearer.'
            }
        ]
    },
    {
        category: 'Shipping & Delivery',
        questions: [
            {
                q: 'Where do you ship from?',
                a: 'All orders are compounded, aged, and dispatched directly from our central laboratory in Lahore, Pakistan.'
            },
            {
                q: 'What are the delivery timelines?',
                a: 'Lahore orders are delivered next working day. Other major cities (Karachi, Islamabad, Rawalpindi, Peshawar, Faisalabad, etc.) take 2 to 4 working days. Remote areas take up to 5 working days.'
            },
            {
                q: 'What are the shipping charges?',
                a: 'We offer free delivery nationwide on all orders above ₨5,000. For orders below ₨5,000, a flat shipping fee of ₨250 is applied at checkout.'
            }
        ]
    },
    {
        category: 'Payments & Packaging',
        questions: [
            {
                q: 'What payment options do you support?',
                a: 'Currently, we support Cash on Delivery (COD) nationwide. You can also pay via JazzCash, EasyPaisa, or direct Bank Transfer by selecting the COD option and coordinating with our client care team on WhatsApp.'
            },
            {
                q: 'How are the fragrances packaged?',
                a: 'Every fragrance is packaged inside a custom Da Scientist Signature Box. The box features serialized batch tracking signatures and a tamper-proof wrap to guarantee authenticity and prevent leakage during transit.'
            }
        ]
    },
    {
        category: 'Returns & Exchange',
        questions: [
            {
                q: 'What is your exchange policy?',
                a: 'We offer a hassle-free exchange within 7 days of delivery. If you are unsatisfied with a fragrance, you can exchange it for any other scent, provided the outer box is sealed and the signature sticker remains untampered.'
            },
            {
                q: 'How do I start a return process?',
                a: 'To request an exchange, contact our Client Care team on WhatsApp with your Order ID. Our logistics partner will schedule a reverse pickup from your address.'
            }
        ]
    }
];

export default function FAQAccordion() {
    const [activeIndex, setActiveIndex] = useState(null);

    const toggleQuestion = (index) => {
        setActiveIndex(activeIndex === index ? null : index);
    };

    let questionGlobalCounter = 0;

    return (
        <div className="luxury-faq-container">
            <div className="faq-header">
                <h2>Frequently Asked Questions</h2>
                <p>Have questions about compounding, delivery timelines, or our scent longevity? Discover our detailed guide below.</p>
            </div>

            <div className="faq-groups">
                {FAQ_DATA.map((group, groupIdx) => (
                    <div key={groupIdx} className="faq-group-section">
                        <h3 className="faq-group-title">{group.category}</h3>
                        <div className="faq-accordion-rows">
                            {group.questions.map((item, itemIdx) => {
                                const currentIdx = questionGlobalCounter;
                                questionGlobalCounter++;
                                const isOpen = activeIndex === currentIdx;

                                return (
                                    <div 
                                        key={itemIdx} 
                                        className={`faq-row ${isOpen ? 'is-open' : ''}`}
                                    >
                                        <button 
                                            className="faq-question-btn" 
                                            onClick={() => toggleQuestion(currentIdx)}
                                            aria-expanded={isOpen}
                                        >
                                            <span>{item.q}</span>
                                            <span className="faq-chevron-icon">
                                                <svg width="10" height="6" viewBox="0 0 10 6" fill="none">
                                                    <path d="M1 1L5 5L9 1" stroke="currentColor" strokeWidth="1.2"/>
                                                </svg>
                                            </span>
                                        </button>
                                        <div className="faq-answer-wrap" style={{ maxHeight: isOpen ? '200px' : '0' }}>
                                            <div className="faq-answer-content">
                                                <p>{item.a}</p>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}
