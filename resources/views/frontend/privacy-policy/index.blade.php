@extends('layouts.frontend.main')
@section('title', 'Company Policies - Elite Guard')

@section('content')
<style>
    .policy-container {
        padding: 120px 0 80px;
        min-height: 100vh;
    }

    .policy-sidebar {
        position: sticky;
        top: 100px;
        background: var(--bg-card);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 25px;
        padding: 30px;
        height: fit-content;
    }

    .policy-nav-link {
        display: block;
        color: var(--text-dim);
        text-decoration: none;
        padding: 12px 15px;
        border-radius: 12px;
        margin-bottom: 5px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .policy-nav-link:hover,
    .policy-nav-link.active {
        background: rgba(139, 92, 246, 0.1);
        color: var(--primary);
        transform: translateX(5px);
    }

    .policy-card {
        background: var(--bg-card);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 35px;
        padding: 50px;
        margin-bottom: 40px;
        transition: border-color 0.3s ease;
        scroll-margin-top: 120px;
    }

    .policy-card:hover {
        border-color: rgba(139, 92, 246, 0.3);
    }

    .policy-title {
        font-size: 2.2rem;
        font-weight: 850;
        margin-bottom: 30px;
        background: linear-gradient(to right, #fff, var(--primary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .policy-section-title {
        color: var(--primary);
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 0.85rem;
        margin-bottom: 15px;
        display: block;
    }

    .policy-content {
        color: var(--text-dim);
        line-height: 1.8;
    }

    .policy-content h3 {
        color: var(--text-main);
        font-weight: 700;
        margin: 40px 0 20px;
        font-size: 1.4rem;
    }

    .policy-content p {
        margin-bottom: 20px;
    }

    .policy-list {
        list-style: none;
        padding: 0;
        margin-bottom: 30px;
    }

    .policy-list li {
        position: relative;
        padding-left: 30px;
        margin-bottom: 15px;
    }

    .policy-list li::before {
        content: '\f058';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        left: 0;
        color: var(--primary);
        font-size: 0.9rem;
    }

    .policy-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }

    .grid-item {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--glass-border);
        padding: 25px;
        border-radius: 20px;
    }

    .grid-item h4 {
        color: var(--text-main);
        font-size: 1.1rem;
        margin-bottom: 15px;
        font-weight: 700;
    }

    .acknowledgment-box {
        background: rgba(16, 185, 129, 0.05);
        border: 1px solid rgba(16, 185, 129, 0.2);
        padding: 30px;
        border-radius: 20px;
        margin-top: 40px;
    }

    .acknowledgment-box h5 {
        color: var(--accent);
        font-weight: 800;
        margin-bottom: 15px;
    }

    .company-footer {
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid var(--glass-border);
        font-size: 0.85rem;
        color: var(--text-dim);
    }

    @media (max-width: 991px) {
        .policy-sidebar {
            position: relative;
            top: 0;
            margin-bottom: 40px;
        }

        .policy-card {
            padding: 30px;
        }

        .policy-title {
            font-size: 1.8rem;
        }
    }
</style>

<div class="policy-container">
    <div class="container">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3">
                <div class="policy-sidebar" data-aos="fade-right">
                    <span class="policy-section-title">Navigation</span>
                    <a href="#employment" class="policy-nav-link active">Employment Policies</a>
                    <a href="#vehicle" class="policy-nav-link">Vehicle Use Policy</a>
                    <a href="#confidentiality" class="policy-nav-link">Confidentiality</a>
                    <a href="#averaging" class="policy-nav-link">Averaging Arrangement</a>
                    <a href="#violence" class="policy-nav-link">Violence & Harassment</a>
                    <a href="#conduct" class="policy-nav-link">Code of Conduct</a>
                    <a href="#complaints" class="policy-nav-link">Public Complaints</a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-lg-9">
                <!-- 1. Employment Policies -->
                <section id="employment" class="policy-card" data-aos="fade-up">
                    <span class="policy-section-title">Section 01</span>
                    <h2 class="policy-title">Employment Policies</h2>
                    <div class="policy-content">
                        <h3>Definitions</h3>
                        <ul class="policy-list">
                            <li><strong>Employee:</strong> Any individual employed or contracted by Elite Guard Inc.</li>
                            <li><strong>Harassment:</strong> Any unwelcome conduct, comment, or action that creates a hostile work environment.</li>
                            <li><strong>Violence:</strong> Any act or threat that may cause physical or psychological harm.</li>
                            <li><strong>Confidential Information:</strong> Any non-public company, client, or operational data.</li>
                        </ul>

                        <p><strong>Probationary Period:</strong> All new employees are subject to a probationary period of ninety (90) days from the date of hire. During this period, performance, conduct, attendance, and suitability will be evaluated. Elite Guard Inc. reserves the right to terminate employment during probation in accordance with applicable provincial employment legislation.</p>

                        <ul class="policy-list">
                            <li><strong>No Shows:</strong> Notify supervisor at least four (4) hours prior to shift start if unable to attend. Failure to report may result in disciplinary action.</li>
                            <li><strong>Being Late:</strong> Arrive at least fifteen (15) minutes prior to shift start. Repeated lateness results in progressive discipline.</li>
                            <li><strong>Site Abandonment:</strong> Leaving site without authorization is strictly prohibited; may result in immediate termination.</li>
                            <li><strong>Shift Confirmations:</strong> Employees must confirm assigned shifts. Failure may result in removal from schedule.</li>
                            <li><strong>Sleeping on Duty:</strong> Must remain awake at all times. Sleeping on shift may result in immediate termination.</li>
                            <li><strong>Personal Devices:</strong> Excessive phone use is prohibited. Devices must remain on silent mode.</li>
                            <li><strong>Time-Off Requests:</strong> Submit at least two (2) weeks in advance. Subject to operational needs.</li>
                            <li><strong>Shift Cancellation:</strong> Must call dispatch directly. Text or email is not acceptable.</li>
                            <li><strong>Harassment Policy:</strong> Zero tolerance for harassment or workplace violence.</li>
                            <li><strong>Social Media:</strong> Posting confidential company/client info or uniforms online is prohibited.</li>
                            <li><strong>Frequent Absences:</strong> May result in reduced scheduling or disciplinary action.</li>
                            <li><strong>Uniform Requirement:</strong> Full designated uniform must be clean and professional at all times.</li>
                            <li><strong>Professional Conduct:</strong> Maintain professional behavior toward clients, coworkers, and the public.</li>
                        </ul>

                        <h3>Disciplinary Process</h3>
                        <ul class="policy-list">
                            <li>Verbal warning</li>
                            <li>Written warning</li>
                            <li>Final warning or suspension</li>
                            <li>Termination (depending on severity)</li>
                        </ul>
                        <p>Elite Guard Inc. reserves the right to bypass progressive discipline in cases of serious misconduct.</p>

                        <div class="acknowledgment-box">
                            <h5>Acknowledgment</h5>
                            <p class="mb-0">By signing below, the Employee confirms they have read and understood these policies and agree to comply with all Elite Guard Inc. requirements.</p>
                        </div>
                    </div>
                </section>

                <!-- 2. Vehicle Use Policy -->
                <section id="vehicle" class="policy-card" data-aos="fade-up">
                    <span class="policy-section-title">Section 02</span>
                    <h2 class="policy-title">Vehicle Use Policy Acknowledgment</h2>
                    <div class="policy-content">
                        <h3>Policy Overview</h3>
                        <p>While operating an Elite Guard Inc. vehicle, collisions or other damage may occur, and such incidents are subject to review by management. Investigations include incident reports, driver statements, and environmental conditions.</p>

                        <div class="policy-grid">
                            <div class="grid-item">
                                <h4>Liability Determination</h4>
                                <p>Liability is assessed case-by-case. Operators at fault may be required to compensate for minor repairs or the insurance deductible (currently $500).</p>
                            </div>
                            <div class="grid-item">
                                <h4>Compensation</h4>
                                <p>At-fault operators must choose cash payment or payroll deduction. Failure to compensate may result in termination or legal action.</p>
                            </div>
                        </div>

                        <div class="acknowledgment-box">
                            <h5>Agreement</h5>
                            <p class="mb-0">By signing this document, the Employee agrees to all terms and confirms a clear understanding of employment conditions.</p>
                        </div>
                    </div>
                </section>

                <!-- 3. Confidentiality Agreement -->
                <section id="confidentiality" class="policy-card" data-aos="fade-up">
                    <span class="policy-section-title">Section 03</span>
                    <h2 class="policy-title">Confidentiality Agreement</h2>
                    <div class="policy-content">
                        <p>This Agreement is accepted upon commencement of work and receipt of payment from Elite Guard Inc. (the “Company”).</p>

                        <ul class="policy-list">
                            <li><strong>Introduction:</strong> Includes operational procedures, client info, security protocols, site assignments, financial data, and personnel records.</li>
                            <li><strong>Non-Disclosure:</strong> Maintain strict confidentiality during and after employment. No third-party disclosure without prior consent.</li>
                            <li><strong>Return of Property:</strong> Immediately return all files, uniforms, ID badges, keys, and equipment upon termination.</li>
                            <li><strong>Non-Solicitation:</strong> For one (1) year post-termination, do not solicit clients or induce employees to leave.</li>
                            <li><strong>Ownership:</strong> All reports and materials created during employment remain exclusive company property.</li>
                            <li><strong>Governing Law:</strong> Governed by provincial laws and the applicable laws of Canada.</li>
                        </ul>
                    </div>
                </section>

                <!-- 4. Averaging Arrangement -->
                <section id="averaging" class="policy-card" data-aos="fade-up">
                    <span class="policy-section-title">Section 04</span>
                    <h2 class="policy-title">Averaging Arrangement Policy</h2>
                    <div class="policy-content">
                        <p>Elite Guard Inc. is committed to flexible work arrangements in accordance with the Alberta Employment Standards Code (ESC).</p>

                        <h3>Definitions</h3>
                        <p><strong>Averaging Arrangement:</strong> Agreement allowing the company to average hours over 1 to 52 weeks to determine overtime pay.</p>

                        <h3>Employer Responsibilities</h3>
                        <ul class="policy-list">
                            <li>Provide written notice at least two weeks in advance.</li>
                            <li>Document averaging weeks and specify work schedules.</li>
                            <li>Outline overtime calculations per ESC standards.</li>
                            <li>Compensate calculated overtime pay or paid time off.</li>
                        </ul>

                        <p><strong>Termination:</strong> Either party may terminate the arrangement with thirty (30) days’ written notice.</p>
                    </div>
                </section>

                <!-- 5. Violence & Harassment -->
                <section id="violence" class="policy-card" data-aos="fade-up">
                    <span class="policy-section-title">Section 05</span>
                    <h2 class="policy-title">Violence & Harassment Prevention</h2>
                    <div class="policy-content">
                        <p>Effective Date: 29 June, 2025 | Review Date: 29 June, 2026</p>

                        <div class="policy-grid">
                            <div class="grid-item">
                                <h3>Investigation Process</h3>
                                <ul class="policy-list">
                                    <li>All complaints will be investigated by a neutral and trained individual.</li>
                                    <li>Information will be collected from all involved parties.</li>
                                    <li>Findings will be documented and appropriate action will be taken.</li>
                                    <li>The complainant will be informed of the outcome.</li>
                                </ul>

                                <h4>Hazard Elimination</h4>
                                <ul class="small mb-0">
                                    <li>No tolerance policy.</li>
                                    <li>Limit high-risk exposure.</li>
                                    <li>Rearrange duties for safety.</li>
                                </ul>
                            </div>
                            <div class="grid-item">
                                <h4>Engineering Controls</h4>
                                <ul class="small mb-0">
                                    <li>Security cameras at sites.</li>
                                    <li>Radio communication devices.</li>
                                    <li>Designated safe zones.</li>
                                </ul>
                            </div>
                        </div>

                        <h3>Reporting Procedures</h3>
                        <p>Workers must immediately report incidents to a supervisor, site lead, or safety officer.</p>
                        <ul class="policy-list">
                            <li><strong>How:</strong> Verbally, Incident Report Form, or email to saqib@eliteguardinc.ca</li>
                            <li><strong>OHS Reporting:</strong> Mandatory notification if serious injury occurs or potential for serious harm exists.</li>
                        </ul>
                    </div>
                </section>

                <!-- 6. Code of Conduct -->
                <section id="conduct" class="policy-card" data-aos="fade-up">
                    <span class="policy-section-title">Section 06</span>
                    <h2 class="policy-title">Code of Conduct</h2>
                    <div class="policy-content">
                        <h3>Training and Compliance</h3>
                        <p>Employees are required to attend all mandatory training sessions and remain compliant with company policies and applicable laws.</p>

                        <h3>Disciplinary Action</h3>
                        <p>Failure to comply with this Code may result in disciplinary action, up to and including termination of employment.</p>

                        <p>Adheres to the Security Services and Investigators Act (SSIA) of Alberta.</p>

                        <div class="policy-grid">
                            <div class="grid-item">
                                <h4>Professionalism</h4>
                                <p class="small mb-0">Integrity, respect for all individuals, strict confidentiality, and avoiding conflicts of interest.</p>
                            </div>
                            <div class="grid-item">
                                <h4>Use of Force</h4>
                                <p class="small mb-0">Exercise restraint; use force only when absolutely necessary, reasonable, and proportionate.</p>
                            </div>
                        </div>

                        <p class="mt-4"><strong>Reporting:</strong> Mandatory reporting of SSIA violations or significant security events to supervisors.</p>
                    </div>
                </section>

                <!-- 7. Public Complaints -->
                <section id="complaints" class="policy-card" data-aos="fade-up" style="margin-bottom: 0;">
                    <span class="policy-section-title">Section 07</span>
                    <h2 class="policy-title">Public Complaints Policy</h2>
                    <div class="policy-content">
                        <p>Provides a process for the public to file complaints regarding security personnel conduct, per SSIA regulations.</p>

                        <h3>Process</h3>
                        <ul class="policy-list">
                            <li><strong>Acknowledgment:</strong> Within 5 business days.</li>
                            <li><strong>Investigation:</strong> Internal review by designated complaints officer.</li>
                            <li><strong>Resolution:</strong> Targeted within 30 days.</li>
                        </ul>

                        <h3>Outcome & Appeal</h3>
                        <ul class="policy-list">
                            <li>Complainant will receive a written response outlining findings.</li>
                            <li>If unsatisfied, a request for review may be submitted to senior management.</li>
                            <li>Serious complaints may be escalated to Alberta Security Programs and Licensing.</li>
                        </ul>

                        <h3>Confidentiality</h3>
                        <p>All complaints will be handled confidentially. Information will only be shared where necessary for investigation or required by law. Individuals submitting complaints in good faith will be protected from retaliation.</p>

                        <div class="acknowledgment-box">
                            <h5>Contact for Complaints</h5>
                            <p class="mb-0">
                                Elite Guard Inc. Complaint Handling Department<br>
                                2104-3961 52nd Ave NE Calgary, AB T3J 0K7<br>
                                Phone: (403) 830-7772 | Email for Complaints: saqib@eliteguardinc.ca
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Company Footer -->
                <div class="company-footer text-center">
                    <h3>Policy Review</h3>
                    <p>All policies are reviewed annually and may be updated to reflect changes in laws, regulations, or company requirements.</p>
                    <p class="mb-1"><strong>ELITE GUARD INC.</strong></p>
                    <p class="mb-1">3961 52 Ave, NE #2104, Calgary, AB T3J0J8</p>
                    <p class="mb-0">Contact No: 403-830-7772 | Email for General Inquiries: info@eliteguardinc.ca</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.policy-nav-link');
        const sections = document.querySelectorAll('section');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 150) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active');
                }
            });
        });
    });
</script>
@endsection