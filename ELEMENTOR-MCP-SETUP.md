# Elementor MCP — Setup dokumentacija

## Šta je potrebno (jednom po sajtu)

### 1. Plugins na WordPress sajtu
Oba moraju biti aktivna u wp-admin → Plugins:
- **MCP Adapter** (folder: `mcp-adapter-trunk`)
- **MCP Tools for Elementor** (folder: `elementor-mcp-main`)

### 2. Composer install za MCP Adapter
```
cd "C:\Users\Nemanja Cicmil\Local Sites\SAJT\app\public\wp-content\plugins\mcp-adapter-trunk"
composer install --no-dev
```
Mora imati `vendor` folder — bez toga MCP Adapter ne radi.

### 3. Application Password
`wp-admin → Users → Profil korisnika → Application Passwords`
→ Napiši bilo koji naziv (npr. `claude`) → Add New
→ Sačuvaj generisanu lozinku (prikazuje se samo jednom)

⚠️ WP_USERNAME je WordPress login ime (npr. `pera`), NE naziv Application Password-a

### 4. C:\tmp folder (jednom)
```
mkdir C:\tmp
```
Potreban za debug log.

---

## Config fajlovi

### Claude Code — `.mcp.json` u root project foldera
```json
{
    "mcpServers": {
        "elementor-mcp": {
            "type": "stdio",
            "command": "node",
            "args": [
                "C:\\Users\\Nemanja Cicmil\\Local Sites\\SAJT\\app\\public\\wp-content\\plugins\\elementor-mcp-main\\bin\\mcp-proxy.mjs"
            ],
            "env": {
                "WP_URL": "http://SAJT.local",
                "WP_USERNAME": "pera",
                "WP_APP_PASSWORD": "xxxx xxxx xxxx xxxx xxxx xxxx",
                "MCP_PROTOCOL_VERSION": "2024-11-05",
                "MCP_LOG_FILE": "C:\\tmp\\elementor-mcp-debug.log"
            }
        }
    }
}
```

### Claude Desktop — `%APPDATA%\Claude\claude_desktop_config.json`
Isto kao gore ali BEZ `"type": "stdio"`, i svaki sajt ima jedinstveno ime:
```json
{
    "mcpServers": {
        "elementor-mcp-SAJT": {
            "command": "node",
            "args": [
                "C:\\Users\\Nemanja Cicmil\\Local Sites\\SAJT\\app\\public\\wp-content\\plugins\\elementor-mcp-main\\bin\\mcp-proxy.mjs"
            ],
            "env": {
                "WP_URL": "http://SAJT.local",
                "WP_USERNAME": "pera",
                "WP_APP_PASSWORD": "xxxx xxxx xxxx xxxx xxxx xxxx",
                "MCP_PROTOCOL_VERSION": "2024-11-05",
                "MCP_LOG_FILE": "C:\\tmp\\elementor-mcp-debug.log"
            }
        }
    }
}
```

---

## Shared hosting (produkcija)

Na shared hostingu je zapravo LAKŠE — koristi se direktna HTTP konekcija, bez Node.js proxy-ja.

### Šta treba na hostingu:
1. Aktivirati oba plugina (uploadovati kao zip)
2. Napraviti Application Password (isto kao lokalno)
3. MCP Adapter na hostingu NE treba composer install — plugin zip sa WordPress.org dolazi sa svim zavisnostima

### Config za Claude Code — `.mcp.json`:
```json
{
    "mcpServers": {
        "elementor-mcp": {
            "type": "http",
            "url": "https://mojsajt.rs/wp-json/mcp/elementor-mcp-server",
            "headers": {
                "Authorization": "Basic BASE64_STRING"
            }
        }
    }
}
```

### Kako dobiti BASE64_STRING:
Iz MCP Tools for Elementor plugina → Connection tab → unesi username i Application Password → klikni "Generate Configs" → kopira sve automatski.

### Config za Claude Desktop — `%APPDATA%\Claude\claude_desktop_config.json`:
```json
{
    "mcpServers": {
        "elementor-mcp-mojsajt": {
            "command": "node",
            "args": ["/putanja/do/mcp-proxy.mjs"],
            "env": {
                "WP_URL": "https://mojsajt.rs",
                "WP_USERNAME": "pera",
                "WP_APP_PASSWORD": "xxxx xxxx xxxx xxxx xxxx xxxx",
                "MCP_PROTOCOL_VERSION": "2024-11-05"
            }
        }
    }
}
```
⚠️ Claude Desktop ne podržava `type: http` — mora node proxy. Proxy može biti lokalno instaliran ili na serveru.

---

## Česte greške i rešenja

| Greška | Uzrok | Rešenje |
|--------|-------|---------|
| `Cannot find module .../mcp-proxy.mjs` | Pogrešna putanja do plugina | Proveri naziv foldera — može biti `elementor-mcp-main` ili `elementor-mcp-1.5.0` |
| `HTTP 401 rest_forbidden` | Pogrešan WP_USERNAME | Username je WordPress login, **ne naziv Application Password-a** |
| `Server disconnected` / `not valid config` | `"type": "http"` u Claude Desktop | Desktop ne podržava HTTP tip — koristi `command/args` |
| `Autoloader not found` | Nije pokrenut composer install | `composer install --no-dev` u `mcp-adapter-trunk` folderu |
| `MCP Tools requires MCP Adapter` | MCP Adapter plugin nije aktivan | Aktiviraj u wp-admin → Plugins |
| Proxy se pokreće ali visi | Local by Flywheel nije pokrenut | Pokreni Local + sajt, pa restartuj Claude |

---

## Za novi lokalni sajt — brzi checklist

- [ ] Kopiraj `elementor-mcp-main` u plugins folder novog sajta
- [ ] Kopiraj `mcp-adapter-trunk` u plugins folder novog sajta
- [ ] Aktiviraj oba plugina u wp-admin
- [ ] Pokreni `composer install --no-dev` u `mcp-adapter-trunk`
- [ ] Napravi Application Password (upamti: username ≠ naziv passworda)
- [ ] Napravi `.mcp.json` u project folderu
- [ ] Restartuj Claude

## Za novi produkcioni sajt — brzi checklist

- [ ] Uploaduj `elementor-mcp-main.zip` i `mcp-adapter.zip` u wp-admin → Plugins → Add New
- [ ] Aktiviraj oba plugina
- [ ] Napravi Application Password
- [ ] Uzmi config iz MCP Tools → Connection → Generate Configs
- [ ] Dodaj u `.mcp.json` (Claude Code) ili Desktop config
- [ ] Restartuj Claude
