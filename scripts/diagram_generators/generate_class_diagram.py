"""
Generate Healthy Bite Final Corrected UML Class Diagram
Preserves college-approved format:
- Standard UML 3-compartment class notation: Class Name, Attributes, Operations
- Represents the true PHP custom MVC architecture:
  1. Core Framework (App, Router, Controller, Database, Auth, Request, Response)
  2. Middleware Layer (AuthMiddleware, RestaurantMiddleware, AdminMiddleware)
  3. Presentation Controllers (Customer, Owner, Admin)
  4. Business Service Layer (Cart, Pricing, Nutrition [8 macros], Order, Payment, QR)
  5. Data Repository Layer (Food, Order, Payment, Restaurant, Review, User Repositories)
Outputs:
- diagrams/07_Class_Diagram.png
- diagrams/class diagram/Healthy Bite UML Class Diagram.png
"""

from PIL import Image, ImageDraw, ImageFont

FONT_PATH = r"C:\Windows\Fonts\segoeui.ttf"
FONT_BOLD_PATH = r"C:\Windows\Fonts\segoeuib.ttf"
FONT_MONO_PATH = r"C:\Windows\Fonts\consola.ttf"

def get_font(size=14, bold=False, mono=False):
    p = FONT_MONO_PATH if mono else (FONT_BOLD_PATH if bold else FONT_PATH)
    try:
        return ImageFont.truetype(p, size)
    except Exception:
        return ImageFont.load_default()

def draw_uml_class(draw, box, class_name, stereotype="", attributes=[], methods=[], header_bg="#1E293B", border_color="#334155"):
    x1, y1, x2, y2 = box
    w = x2 - x1
    draw.rounded_rectangle([x1, y1, x2, y2], radius=6, fill="#FFFFFF", outline=border_color, width=2)

    # 1. Header compartment
    hdr_h = 44 if stereotype else 36
    draw.rounded_rectangle([x1, y1, x2, y1 + hdr_h], radius=6, fill=header_bg)
    draw.rectangle([x1, y1 + hdr_h - 6, x2, y1 + hdr_h], fill=header_bg)

    f_title = get_font(13, bold=True)
    if stereotype:
        f_ste = get_font(10, bold=False)
        draw.text((x1 + w // 2 - len(stereotype) * 3, y1 + 4), stereotype, font=f_ste, fill="#94A3B8")
        draw.text((x1 + w // 2 - len(class_name) * 4, y1 + 18), class_name, font=f_title, fill="#FFFFFF")
    else:
        draw.text((x1 + w // 2 - len(class_name) * 4, y1 + 9), class_name, font=f_title, fill="#FFFFFF")

    # Divider 1
    draw.line([(x1, y1 + hdr_h), (x2, y1 + hdr_h)], fill=border_color, width=1)

    # 2. Attributes compartment
    attr_line_h = 17
    curr_y = y1 + hdr_h + 4
    f_code = get_font(10, mono=True)
    for a in attributes:
        draw.text((x1 + 8, curr_y), a, font=f_code, fill="#0F172A")
        curr_y += attr_line_h

    # Divider 2
    div2_y = y1 + hdr_h + max(len(attributes), 1) * attr_line_h + 8
    draw.line([(x1, div2_y), (x2, div2_y)], fill=border_color, width=1)

    # 3. Methods compartment
    curr_y = div2_y + 4
    for m in methods:
        draw.text((x1 + 8, curr_y), m, font=f_code, fill="#0369A1")
        curr_y += attr_line_h

def draw_dependency_arrow(draw, p1, p2, label="", fill="#64748B", dashed=True):
    x1, y1 = p1
    x2, y2 = p2
    draw.line([p1, p2], fill=fill, width=2)
    # arrow head at p2
    sz = 7
    if y2 > y1: # downwards
        draw.polygon([(x2, y2), (x2 - sz, y2 - sz * 1.5), (x2 + sz, y2 - sz * 1.5)], fill=fill)
    elif y1 > y2: # upwards
        draw.polygon([(x2, y2), (x2 - sz, y2 + sz * 1.5), (x2 + sz, y2 + sz * 1.5)], fill=fill)
    elif x2 > x1: # rightwards
        draw.polygon([(x2, y2), (x2 - sz * 1.5, y2 - sz), (x2 - sz * 1.5, y2 + sz)], fill=fill)
    else: # leftwards
        draw.polygon([(x2, y2), (x2 + sz * 1.5, y2 - sz), (x2 + sz * 1.5, y2 + sz)], fill=fill)

    if label:
        f = get_font(10, bold=True)
        draw.text(((x1 + x2) // 2 + 4, (y1 + y2) // 2 - 10), label, font=f, fill="#475569")

def generate():
    canvas_w = 4000
    canvas_h = 2800
    img = Image.new("RGB", (canvas_w, canvas_h), "#F8FAFC")
    draw = ImageDraw.Draw(img)

    # Banner
    draw.rectangle([0, 0, canvas_w, 90], fill="#0F172A")
    draw.text((40, 18), "HEALTHY BITE — UML ARCHITECTURAL CLASS DIAGRAM", font=get_font(24, bold=True), fill="#FFFFFF")
    draw.text((40, 56), "Custom PHP 8.2+ MVC Architecture • Core Framework • Middleware • Controllers • Services • Repositories • PDO MySQL", font=get_font(13), fill="#94A3B8")

    # ================= 1. CORE ARCHITECTURE TIER (Y: 130 to 480) =================
    core_classes = [
        # (name, ste, attrs, methods, box)
        ("App\\Core\\App", "<<Singleton>>", ["-router: Router", "-db: Database"], ["+run(): void", "+bootstrap(): void"], (60, 140, 360, 310)),
        ("App\\Core\\Router", "", ["-routes: array", "-middlewares: array"], ["+get(path, handler): void", "+post(path, handler): void", "+dispatch(req): void"], (400, 140, 720, 310)),
        ("App\\Core\\Request", "", ["-method: string", "-uri: string", "-body: array"], ["+getMethod(): string", "+getPath(): string", "+getBody(): array", "+getParam(k): mixed"], (760, 140, 1080, 310)),
        ("App\\Core\\Response", "", ["-statusCode: int", "-headers: array"], ["+setStatusCode(c): void", "+json(data): void", "+redirect(url): void"], (1120, 140, 1440, 310)),
        ("App\\Core\\Controller", "<<Abstract>>", ["#request: Request", "#response: Response"], ["+render(view, data): void", "+json(data): void", "+validate(data, r): bool"], (1480, 140, 1820, 310)),
        ("App\\Core\\Database", "<<Singleton>>", ["-connection: PDO", "-instance: Database"], ["+getInstance(): Database", "+getConnection(): PDO"], (1860, 140, 2180, 310)),
        ("App\\Core\\Auth", "", ["-session: Session", "-userRepo: UserRepository"], ["+check(): bool", "+user(): ?array", "+hasRole(role): bool", "+login(u): void", "+logout(): void"], (2220, 140, 2560, 310)),
    ]

    for name, ste, attrs, meths, box in core_classes:
        draw_uml_class(draw, box, name, stereotype=ste, attributes=attrs, methods=meths, header_bg="#0F172A", border_color="#334155")

    # ================= 2. MIDDLEWARE LAYER (Y: 370 to 560) =================
    mw_classes = [
        ("AuthMiddleware", "<<Middleware>>", ["-auth: Auth"], ["+handle(req, next): mixed"], (400, 380, 720, 520)),
        ("RestaurantMiddleware", "<<Middleware>>", ["-db: PDO"], ["+handle(req, next): mixed"], (760, 380, 1080, 520)),
        ("AdminMiddleware", "<<Middleware>>", ["-auth: Auth"], ["+handle(req, next): mixed"], (1120, 380, 1440, 520)),
    ]
    for name, ste, attrs, meths, box in mw_classes:
        draw_uml_class(draw, box, name, stereotype=ste, attributes=attrs, methods=meths, header_bg="#475569", border_color="#64748B")

    # ================= 3. PRESENTATION CONTROLLERS (Y: 600 to 950) =================
    ctrl_classes = [
        ("Customer\\MenuController", "<<Controller>>", ["-menuService: MenuService", "-qrService: QrService"], ["+index(): void", "+show(dishId): void", "+resolveQr(token): void"], (60, 600, 440, 800)),
        ("Customer\\CheckoutController", "<<Controller>>", ["-cartService: CartService", "-orderService: OrderService", "-payService: PaymentService"], ["+index(): void", "+process(): void", "+confirmation(no): void", "+tracking(no): void"], (480, 600, 880, 820)),
        ("Owner\\OrderController", "<<Controller>>", ["-orderService: OrderService"], ["+index(): void", "+kitchenOrders(): void", "+updateStatus(id): void"], (920, 600, 1300, 800)),
        ("Owner\\MenuController", "<<Controller>>", ["-foodRepo: FoodRepository", "-catRepo: CategoryRepository"], ["+index(): void", "+store(): void", "+update(id): void", "+toggleAvailability(id): void"], (1340, 600, 1740, 820)),
        ("Owner\\TableController", "<<Controller>>", ["-qrService: QrService"], ["+index(): void", "+generateQr(tblId): void"], (1780, 600, 2140, 800)),
        ("Owner\\AnalyticsController", "<<Controller>>", ["-orderRepo: OrderRepository"], ["+index(): void", "+export(): void"], (2180, 600, 2520, 800)),
        ("Admin\\RestaurantController", "<<Controller>>", ["-restRepo: RestaurantRepository"], ["+index(): void", "+updateStatus(id): void", "+inspect(id): void"], (2560, 600, 2920, 800)),
    ]
    for name, ste, attrs, meths, box in ctrl_classes:
        draw_uml_class(draw, box, name, stereotype=ste, attributes=attrs, methods=meths, header_bg="#1E3A8A", border_color="#2563EB")

    # ================= 4. BUSINESS SERVICE LAYER (Y: 1040 to 1450) =================
    service_classes = [
        ("CartService", "<<Service>>", ["-foodRepo: FoodRepository", "-pricing: PricingService", "-nutrition: NutritionService"], ["+validateCart(items): array", "+calculateTotals(items): array", "+getPairings(items): array"], (60, 1040, 460, 1280)),
        ("PricingService", "<<Service>>", [], ["+calculateItemPrice(base, var, customs): float", "+calculateOrderSubtotal(items): float", "+calculateTax(subtotal): float"], (500, 1040, 920, 1280)),
        ("NutritionService", "<<Service>>", [], ["+calculateItemMacros(base, var, customs): array", "+calculateOrderMacros(items): array", "/* 8 Macros: kcal, protein, carbs, */", "/* fat, fiber, sugar, sodium, caffeine */"], (960, 1040, 1400, 1300)),
        ("OrderService", "<<Service>>", ["-orderRepo: OrderRepository", "-payService: PaymentService"], ["+createOrder(orderData, items): int", "+updateOrderStatus(id, status): bool", "+getLiveKitchenOrders(branchId): array"], (1440, 1040, 1860, 1280)),
        ("PaymentService", "<<Service>>", ["-payRepo: PaymentRepository"], ["+processSimulatedPayment(orderId, m, amt): array", "+verifyPayment(ref): bool", "+recordSettlement(data): int"], (1900, 1040, 2340, 1280)),
        ("QrService", "<<Service>>", ["-db: PDO"], ["+generateToken(tableId, branchId): string", "+resolveToken(token): ?array", "+validateTokenExpiry(token): bool"], (2380, 1040, 2800, 1280)),
        ("MenuService", "<<Service>>", ["-foodRepo: FoodRepository", "-catRepo: CategoryRepository"], ["+getRestaurantMenu(restId): array", "+getDishDetails(dishId): ?array"], (2840, 1040, 3240, 1280)),
    ]
    for name, ste, attrs, meths, box in service_classes:
        draw_uml_class(draw, box, name, stereotype=ste, attributes=attrs, methods=meths, header_bg="#065F46", border_color="#059669")

    # ================= 5. REPOSITORY LAYER (Y: 1540 to 2000) =================
    repo_classes = [
        ("FoodRepository", "<<Repository>>", ["-db: PDO"], ["+getByRestaurant(restId): array", "+getById(id): ?array", "+updateAvailability(id, st): bool", "+create(data): int"], (60, 1540, 440, 1780)),
        ("OrderRepository", "<<Repository>>", ["-db: PDO"], ["+createOrder(data): int", "+createItemSnapshot(data): int", "+createCustomSnapshot(data): int", "+findByNumber(num): ?array", "+updateStatus(id, st): bool"], (480, 1540, 900, 1800)),
        ("PaymentRepository", "<<Repository>>", ["-db: PDO"], ["+recordPayment(data): int", "+findByOrderId(orderId): ?array", "+updateStatus(id, st): bool"], (940, 1540, 1340, 1780)),
        ("RestaurantRepository", "<<Repository>>", ["-db: PDO"], ["+findById(id): ?array", "+findBySlug(slug): ?array", "+updateStatus(id, status): bool", "+getAllTenants(): array"], (1380, 1540, 1780, 1780)),
        ("CategoryRepository", "<<Repository>>", ["-db: PDO"], ["+getByRestaurant(restId): array", "+findById(id): ?array", "+create(data): int"], (1820, 1540, 2200, 1780)),
        ("ReviewRepository", "<<Repository>>", ["-db: PDO"], ["+createReview(data): int", "+getByRestaurant(restId): array", "+addOwnerReply(id, reply): bool"], (2240, 1540, 2640, 1780)),
        ("UserRepository", "<<Repository>>", ["-db: PDO"], ["+findByEmail(email): ?array", "+findById(id): ?array", "+create(data): int", "+updateLastLogin(id): void"], (2680, 1540, 3060, 1780)),
    ]
    for name, ste, attrs, meths, box in repo_classes:
        draw_uml_class(draw, box, name, stereotype=ste, attributes=attrs, methods=meths, header_bg="#78350F", border_color="#D97706")

    # ================= 6. DATA LAYER (Y: 2100 to 2300) =================
    draw.rounded_rectangle([1000, 2120, 2200, 2260], radius=10, fill="#FEF2F2", outline="#DC2626", width=2)
    draw.text((1050, 2145), "MySQL 8.x / MariaDB Database (`healthy_bite`)", font=get_font(18, bold=True), fill="#991B1B")
    draw.text((1050, 2185), "17 Physical Tables • 26 Foreign Key Constraints • Strict 1:1 Payment Settlement • Snapshot Integrity", font=get_font(13), fill="#7F1D1D")

    # Connect Layers with Dependency Arrows
    # Controllers -> Services
    draw_dependency_arrow(draw, (250, 800), (250, 1040), label="uses")
    draw_dependency_arrow(draw, (680, 820), (680, 1040), label="uses")
    draw_dependency_arrow(draw, (1110, 800), (1650, 1040), label="delegates")
    draw_dependency_arrow(draw, (1540, 820), (250, 1540), label="calls")
    draw_dependency_arrow(draw, (1960, 800), (2590, 1040), label="calls")

    # Services -> Repositories
    draw_dependency_arrow(draw, (260, 1280), (250, 1540), label="queries")
    draw_dependency_arrow(draw, (1650, 1280), (690, 1540), label="persists")
    draw_dependency_arrow(draw, (2120, 1280), (1140, 1540), label="persists")
    draw_dependency_arrow(draw, (3040, 1280), (250, 1540), label="reads")

    # Repositories -> Database
    for rx in [250, 690, 1140, 1580, 2010, 2440, 2870]:
        draw_dependency_arrow(draw, (rx, 1780 if rx != 690 else 1800), (1600, 2120), fill="#DC2626", dashed=True)

    # Architectural Summary / Legend Box
    leg_x = 3150
    leg_y = 1540
    draw.rounded_rectangle([leg_x, leg_y, leg_x + 800, leg_y + 700], radius=8, fill="#FFFFFF", outline="#CBD5E1", width=2)
    draw.text((leg_x + 20, leg_y + 20), "ARCHITECTURAL PARADIGM AUDIT & TRUTH:", font=get_font(14, bold=True), fill="#0F172A")
    notes = [
        "1. REALITY: Healthy Bite uses custom PHP MVC with Service-Repository",
        "   pattern, NOT an Active Record / ORM domain model.",
        "2. app/Models/ contains only .gitkeep. Repositories return associative",
        "   arrays (PDO::FETCH_ASSOC) directly from MySQL.",
        "3. NO CLASS INHERITANCE FOR ROLES: Admin, Owner, Manager, Staff are",
        "   NOT subclasses of User; roles are resolved via MySQL `role_id`.",
        "4. DINING TABLE COLUMNS: Physical columns are `table_number, status`,",
        "   NOT invented `table_type` or `capacity` attributes.",
        "5. ALL 8 MACROS: NutritionService & FoodRepository compute & persist",
        "   kcal, protein, carbs, fat, fiber, sugar, sodium, and caffeine.",
        "6. IMMUTABLE SNAPSHOTS: OrderRepository persists frozen snapshots of",
        "   food_name, base_price, variant_name, and customizations in line items.",
        "7. STRICT 1:1 PAYMENT: PaymentService & PaymentRepository enforce",
        "   exactly one payment settlement per order."
    ]
    cy = leg_y + 60
    for n in notes:
        draw.text((leg_x + 20, cy), n, font=get_font(11), fill="#334155")
        cy += 24

    out1 = r"d:\NEW healthy bite\diagrams\07_Class_Diagram.png"
    out2 = r"d:\NEW healthy bite\diagrams\class diagram\Healthy Bite UML Class Diagram.png"
    img.save(out1, "PNG", dpi=(300, 300))
    img.save(out2, "PNG", dpi=(300, 300))
    print(f"Generated Class Diagram: {out1} & {out2} ({canvas_w}x{canvas_h})")

if __name__ == "__main__":
    generate()
