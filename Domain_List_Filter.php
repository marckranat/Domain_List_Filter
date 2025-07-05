<?php
/**
 * Domain List Filter - Advanced Domain Cleanup and Comparison Tool
 * 
 * This tool cleans up domain lists by removing protocols, www prefixes, 
 * trailing slashes, and duplicates. It also organizes subdomains under 
 * their apex domains for better clarity.
 * 
 * Repository: https://github.com/marckranat/Domain_List_Filter
 * 
 * @author Your Name
 * @version 2.0
 */

class DomainListFilter {
    
    const MAX_ENTRIES = 5000;
    
    /**
     * Clean and normalize a single domain
     * 
     * @param string $domain Raw domain input
     * @return string|null Cleaned domain or null if invalid
     */
    private function cleanDomain($domain) {
        if (empty($domain)) {
            return null;
        }
        
        // Remove whitespace
        $domain = trim($domain);
        
        // Remove protocol (http://, https://, ftp://, etc.)
        $domain = preg_replace('/^[a-zA-Z][a-zA-Z0-9+.-]*:\/\//', '', $domain);
        
        // Remove www prefix
        $domain = preg_replace('/^www\./i', '', $domain);
        
        // Remove trailing slash and path
        $domain = preg_replace('/\/.*$/', '', $domain);
        
        // Remove port numbers
        $domain = preg_replace('/:\d+$/', '', $domain);
        
        // Convert to lowercase
        $domain = strtolower($domain);
        
        // Validate domain format
        if (!$this->isValidDomain($domain)) {
            return null;
        }
        
        return $domain;
    }
    
    /**
     * Validate domain format
     * 
     * @param string $domain Domain to validate
     * @return bool True if valid domain
     */
    private function isValidDomain($domain) {
        // Basic domain validation
        if (strlen($domain) > 253) {
            return false;
        }
        
        // Check for valid characters and structure
        return preg_match('/^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?(\.[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?)*$/i', $domain);
    }
    
    /**
     * Get the apex domain from a subdomain
     * 
     * @param string $domain Full domain
     * @return string Apex domain
     */
    private function getApexDomain($domain) {
        $parts = explode('.', $domain);
        
        // If only 2 parts, it's already an apex domain
        if (count($parts) <= 2) {
            return $domain;
        }
        
        // Return last two parts as apex domain
        return implode('.', array_slice($parts, -2));
    }
    
    /**
     * Process and clean a list of domains
     * 
     * @param string $input Raw domain list input
     * @return array Cleaned and organized domains
     */
    public function processDomainList($input) {
        if (empty($input)) {
            return [];
        }
        
        // Split by newlines and clean each domain
        $domains = array_filter(explode("\n", $input), 'trim');
        
        // Limit to MAX_ENTRIES
        if (count($domains) > self::MAX_ENTRIES) {
            $domains = array_slice($domains, 0, self::MAX_ENTRIES);
        }
        
        $cleanedDomains = [];
        
        foreach ($domains as $domain) {
            $cleaned = $this->cleanDomain($domain);
            if ($cleaned !== null) {
                $cleanedDomains[] = $cleaned;
            }
        }
        
        // Remove duplicates
        $cleanedDomains = array_unique($cleanedDomains);
        
        // Group by apex domain
        return $this->groupByApexDomain($cleanedDomains);
    }
    
    /**
     * Group domains by their apex domain
     * 
     * @param array $domains List of cleaned domains
     * @return array Grouped domains
     */
    private function groupByApexDomain($domains) {
        $grouped = [];
        
        foreach ($domains as $domain) {
            $apex = $this->getApexDomain($domain);
            
            if (!isset($grouped[$apex])) {
                $grouped[$apex] = [];
            }
            
            // Add the domain to the appropriate group
            if ($domain === $apex) {
                // This is the apex domain itself
                array_unshift($grouped[$apex], $domain);
            } else {
                // This is a subdomain
                $grouped[$apex][] = $domain;
            }
        }
        
        // Sort each group
        foreach ($grouped as $apex => $domainList) {
            sort($grouped[$apex]);
        }
        
        // Sort by apex domain
        ksort($grouped);
        
        return $grouped;
    }
    
    /**
     * Compare two domain lists
     * 
     * @param array $listA First domain list
     * @param array $listB Second domain list
     * @return array Comparison results
     */
    public function compareDomainLists($listA, $listB) {
        $flatA = $this->flattenGroupedDomains($listA);
        $flatB = $this->flattenGroupedDomains($listB);
        
        return [
            'only_in_a' => $this->groupByApexDomain(array_diff($flatA, $flatB)),
            'only_in_b' => $this->groupByApexDomain(array_diff($flatB, $flatA)),
            'in_both' => $this->groupByApexDomain(array_intersect($flatA, $flatB)),
            'combined' => $this->groupByApexDomain(array_unique(array_merge($flatA, $flatB)))
        ];
    }
    
    /**
     * Flatten grouped domains into a simple array
     * 
     * @param array $grouped Grouped domains
     * @return array Flat array of domains
     */
    public function flattenGroupedDomains($grouped) {
        $flat = [];
        foreach ($grouped as $apex => $domains) {
            $flat = array_merge($flat, $domains);
        }
        return $flat;
    }
}

// Initialize the filter
$filter = new DomainListFilter();
$results = null;
$error = null;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    try {
        $listA = $_POST['list_a'] ?? '';
        $listB = $_POST['list_b'] ?? '';
        
        // Sanitize input
        $listA = htmlspecialchars($listA, ENT_QUOTES, 'UTF-8');
        $listB = htmlspecialchars($listB, ENT_QUOTES, 'UTF-8');
        
        // Process the lists
        $processedA = $filter->processDomainList($listA);
        $processedB = $filter->processDomainList($listB);
        
        // Compare the lists
        $results = $filter->compareDomainLists($processedA, $processedB);
        
    } catch (Exception $e) {
        $error = "An error occurred while processing your domains: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domain List Filter - Advanced Domain Cleanup Tool</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
            margin-bottom: 20px;
        }
        
        .repo-link {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .repo-link:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        
        .main-content {
            padding: 30px;
        }
        
        .form-section {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            flex: 1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            resize: vertical;
            transition: border-color 0.3s ease;
        }
        
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .results-section {
            margin-top: 30px;
        }
        
        .result-group {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .result-header {
            background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
            color: white;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 1.1em;
        }
        
        .result-content {
            padding: 20px;
        }
        
                .domain-group {
            margin-bottom: 5px;
        }
        
        .apex-domain {
            font-weight: normal;
            color: #2c3e50;
            font-size: 0.85em;
            margin-bottom: 2px;
            padding: 2px 4px;
            font-family: 'Courier New', monospace;
            display: block;
        }
        
        .subdomain {
            margin-left: 15px;
            padding: 2px 4px;
            color: #7f8c8d;
            font-family: 'Courier New', monospace;
            font-size: 0.85em;
            display: block;
            margin-bottom: 2px;
        }
        
        .domain-list {
            line-height: 1.3;
        }
        
        .stats {
            display: flex;
            justify-content: space-around;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: 600;
            color: #3498db;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .error {
            background: #e74c3c;
            color: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .info-box {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
            color: #155724;
        }
        
        .info-box strong {
            color: #0f5132;
        }
        
        .copy-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 10px;
            transition: all 0.3s ease;
        }
        
        .copy-btn:hover {
            background: #218838;
            transform: translateY(-1px);
        }
        
        .copy-btn:active {
            transform: translateY(0);
        }
        
        .copy-success {
            background: #17a2b8 !important;
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }
            
            .stats {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌐 Domain List Filter</h1>
            <p>Advanced domain cleanup and comparison tool with subdomain organization</p>
            <a href="https://github.com/marckranat/Domain_List_Filter" class="repo-link" target="_blank">
                📁 View on GitHub
            </a>
        </div>
        
        <div class="main-content">
            <div class="info-box">
                <strong>Features:</strong> Removes protocols (http/https), www prefixes, trailing slashes, and duplicates. 
                Organizes subdomains under their apex domains. Limited to <?php echo DomainListFilter::MAX_ENTRIES; ?> entries per list.
            </div>
            
            <?php if ($error): ?>
                <div class="error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="form-section">
                <div class="form-row">
                    <div class="form-group">
                        <label for="list_a">📋 Domain List A:</label>
                        <textarea name="list_a" id="list_a" rows="12" cols="50" 
                                  placeholder="Enter domains, one per line...&#10;example.com&#10;https://www.subdomain.example.com/&#10;another-domain.org"><?php echo isset($_POST['list_a']) ? htmlspecialchars($_POST['list_a']) : ''; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="list_b">📋 Domain List B:</label>
                        <textarea name="list_b" id="list_b" rows="12" cols="50" 
                                  placeholder="Enter domains, one per line...&#10;example.com&#10;https://www.test.example.net/&#10;different-domain.com"><?php echo isset($_POST['list_b']) ? htmlspecialchars($_POST['list_b']) : ''; ?></textarea>
                    </div>
                </div>
                <button type="submit" name="submit" class="submit-btn">🔍 Filter & Compare</button>
            </form>
            
            <?php if ($results): ?>
                <div class="results-section">
                    <?php
                    $statsA = count($filter->flattenGroupedDomains($results['only_in_a']));
                    $statsB = count($filter->flattenGroupedDomains($results['only_in_b']));
                    $statsBoth = count($filter->flattenGroupedDomains($results['in_both']));
                    $statsTotal = count($filter->flattenGroupedDomains($results['combined']));
                    ?>
                    
                    <div class="stats">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $statsA; ?></div>
                            <div class="stat-label">Only in A</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $statsB; ?></div>
                            <div class="stat-label">Only in B</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $statsBoth; ?></div>
                            <div class="stat-label">In Both</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $statsTotal; ?></div>
                            <div class="stat-label">Total Unique</div>
                        </div>
                    </div>
                    
                    <div class="result-group">
                        <div class="result-header">
                            📊 Domains Only in List A (<?php echo $statsA; ?>)
                            <button class="copy-btn" onclick="copyToClipboard('list-a-only')">Copy</button>
                        </div>
                        <div class="result-content" id="list-a-only">
                            <?php if (empty($results['only_in_a'])): ?>
                                <p style="color: #7f8c8d; font-style: italic;">No unique domains found in List A</p>
                            <?php else: ?>
                                <div class="domain-list">
                                    <?php foreach ($results['only_in_a'] as $apex => $domains): ?>
                                        <div class="domain-group">
                                            <div class="apex-domain"><?php echo htmlspecialchars($apex); ?></div>
                                            <?php foreach ($domains as $domain): ?>
                                                <?php if ($domain !== $apex): ?>
                                                    <div class="subdomain"><?php echo htmlspecialchars($domain); ?></div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="result-group">
                        <div class="result-header">
                            📊 Domains Only in List B (<?php echo $statsB; ?>)
                            <button class="copy-btn" onclick="copyToClipboard('list-b-only')">Copy</button>
                        </div>
                        <div class="result-content" id="list-b-only">
                            <?php if (empty($results['only_in_b'])): ?>
                                <p style="color: #7f8c8d; font-style: italic;">No unique domains found in List B</p>
                            <?php else: ?>
                                <div class="domain-list">
                                    <?php foreach ($results['only_in_b'] as $apex => $domains): ?>
                                        <div class="domain-group">
                                            <div class="apex-domain"><?php echo htmlspecialchars($apex); ?></div>
                                            <?php foreach ($domains as $domain): ?>
                                                <?php if ($domain !== $apex): ?>
                                                    <div class="subdomain"><?php echo htmlspecialchars($domain); ?></div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="result-group">
                        <div class="result-header">
                            🔄 Domains in Both Lists (<?php echo $statsBoth; ?>)
                            <button class="copy-btn" onclick="copyToClipboard('list-both')">Copy</button>
                        </div>
                        <div class="result-content" id="list-both">
                            <?php if (empty($results['in_both'])): ?>
                                <p style="color: #7f8c8d; font-style: italic;">No common domains found</p>
                            <?php else: ?>
                                <div class="domain-list">
                                    <?php foreach ($results['in_both'] as $apex => $domains): ?>
                                        <div class="domain-group">
                                            <div class="apex-domain"><?php echo htmlspecialchars($apex); ?></div>
                                            <?php foreach ($domains as $domain): ?>
                                                <?php if ($domain !== $apex): ?>
                                                    <div class="subdomain"><?php echo htmlspecialchars($domain); ?></div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="result-group">
                        <div class="result-header">
                            📋 Combined Lists (<?php echo $statsTotal; ?> unique domains)
                            <button class="copy-btn" onclick="copyToClipboard('list-combined')">Copy</button>
                        </div>
                        <div class="result-content" id="list-combined">
                            <div class="domain-list">
                                <?php foreach ($results['combined'] as $apex => $domains): ?>
                                    <div class="domain-group">
                                        <div class="apex-domain"><?php echo htmlspecialchars($apex); ?></div>
                                        <?php foreach ($domains as $domain): ?>
                                            <?php if ($domain !== $apex): ?>
                                                <div class="subdomain"><?php echo htmlspecialchars($domain); ?></div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            const button = document.querySelector(`[onclick="copyToClipboard('${elementId}')"]`);
            
            // Get plain text content
            let textContent = '';
            const domainGroups = element.querySelectorAll('.domain-group');
            
            domainGroups.forEach(group => {
                const apexDomain = group.querySelector('.apex-domain').textContent.trim();
                const subdomains = group.querySelectorAll('.subdomain');
                
                textContent += apexDomain + '\n';
                
                subdomains.forEach(subdomain => {
                    textContent += subdomain.textContent.trim() + '\n';
                });
            });
            
            // Copy to clipboard
            navigator.clipboard.writeText(textContent.trim()).then(() => {
                button.textContent = 'Copied!';
                button.classList.add('copy-success');
                
                setTimeout(() => {
                    button.textContent = 'Copy';
                    button.classList.remove('copy-success');
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
                alert('Failed to copy to clipboard');
            });
        }
    </script>
</body>
</html>
