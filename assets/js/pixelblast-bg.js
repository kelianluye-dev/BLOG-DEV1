/**
 * PixelBlast Background — port vanilla JS du composant React
 * Utilise Three.js (CDN) avec le shader GLSL original.
 * Props utilisées : variant=square, pixelSize=4, color=#B19EEF,
 * patternScale=2, patternDensity=1, enableRipples, rippleSpeed=0.4,
 * rippleThickness=0.12, rippleIntensityScale=1.5, speed=0.5, edgeFade=0.25
 */
(function () {
    'use strict';

    if (typeof THREE === 'undefined') return;

    /* ── Config (miroir des props passées au composant) ─────────── */
    var CFG = {
        pixelSize:            4,
        color:                '#B19EEF',
        patternScale:         2,
        patternDensity:       1.4,
        pixelSizeJitter:      0,
        enableRipples:        true,
        rippleSpeed:          0.4,
        rippleThickness:      0.12,
        rippleIntensityScale: 1.5,
        speed:                0.5,
        edgeFade:             0.25,
    };

    var MAX_CLICKS = 10;

    /* ── Shaders (identiques au composant React) ─────────────────── */
    var VERTEX_SRC = /* glsl */`
void main() {
  gl_Position = vec4(position, 1.0);
}`;

    var FRAGMENT_SRC = /* glsl */`
precision highp float;

uniform vec3  uColor;
uniform vec2  uResolution;
uniform float uTime;
uniform float uPixelSize;
uniform float uScale;
uniform float uDensity;
uniform float uPixelJitter;
uniform int   uEnableRipples;
uniform float uRippleSpeed;
uniform float uRippleThickness;
uniform float uRippleIntensity;
uniform float uEdgeFade;
uniform int   uShapeType;

const int SHAPE_SQUARE   = 0;
const int SHAPE_CIRCLE   = 1;
const int SHAPE_TRIANGLE = 2;
const int SHAPE_DIAMOND  = 3;

const int MAX_CLICKS = 10;
uniform vec2  uClickPos  [MAX_CLICKS];
uniform float uClickTimes[MAX_CLICKS];

out vec4 fragColor;

/* ── Bayer ordered dithering ── */
float Bayer2(vec2 a) {
  a = floor(a);
  return fract(a.x / 2. + a.y * a.y * .75);
}
#define Bayer4(a) (Bayer2(.5*(a))*0.25+Bayer2(a))
#define Bayer8(a) (Bayer4(.5*(a))*0.25+Bayer2(a))

/* ── Value noise + FBM ── */
#define FBM_OCTAVES    5
#define FBM_LACUNARITY 1.25
#define FBM_GAIN       1.0

float hash11(float n){ return fract(sin(n)*43758.5453); }

float vnoise(vec3 p){
  vec3 ip=floor(p); vec3 fp=fract(p);
  float n000=hash11(dot(ip+vec3(0,0,0),vec3(1,57,113)));
  float n100=hash11(dot(ip+vec3(1,0,0),vec3(1,57,113)));
  float n010=hash11(dot(ip+vec3(0,1,0),vec3(1,57,113)));
  float n110=hash11(dot(ip+vec3(1,1,0),vec3(1,57,113)));
  float n001=hash11(dot(ip+vec3(0,0,1),vec3(1,57,113)));
  float n101=hash11(dot(ip+vec3(1,0,1),vec3(1,57,113)));
  float n011=hash11(dot(ip+vec3(0,1,1),vec3(1,57,113)));
  float n111=hash11(dot(ip+vec3(1,1,1),vec3(1,57,113)));
  vec3 w=fp*fp*fp*(fp*(fp*6.-15.)+10.);
  float x00=mix(n000,n100,w.x); float x10=mix(n010,n110,w.x);
  float x01=mix(n001,n101,w.x); float x11=mix(n011,n111,w.x);
  float y0=mix(x00,x10,w.y);    float y1=mix(x01,x11,w.y);
  return mix(y0,y1,w.z)*2.-1.;
}

float fbm2(vec2 uv,float t){
  vec3 p=vec3(uv*uScale,t);
  float amp=1.,freq=1.,sum=1.;
  for(int i=0;i<FBM_OCTAVES;++i){
    sum+=amp*vnoise(p*freq);
    freq*=FBM_LACUNARITY; amp*=FBM_GAIN;
  }
  return sum*.5+.5;
}

/* ── Shape masks ── */
float maskCircle(vec2 p,float cov){
  float r=sqrt(cov)*.25;
  float d=length(p-.5)-r;
  float aa=.5*fwidth(d);
  return cov*(1.-smoothstep(-aa,aa,d*2.));
}
float maskTriangle(vec2 p,vec2 id,float cov){
  bool flip=mod(id.x+id.y,2.)>.5;
  if(flip) p.x=1.-p.x;
  float r=sqrt(cov);
  float d=p.y-r*(1.-p.x);
  float aa=fwidth(d);
  return cov*clamp(.5-d/aa,0.,1.);
}
float maskDiamond(vec2 p,float cov){
  float r=sqrt(cov)*.564;
  return step(abs(p.x-.49)+abs(p.y-.49),r);
}

void main(){
  vec2 fragCoord = gl_FragCoord.xy - uResolution*.5;
  float aspect   = uResolution.x/uResolution.y;

  vec2 pixelId = floor(fragCoord/uPixelSize);
  vec2 pixelUV = fract(fragCoord/uPixelSize);

  float cellPx   = 8.*uPixelSize;
  vec2 cellCoord = floor(fragCoord/cellPx)*cellPx;
  vec2 uv        = cellCoord/uResolution*vec2(aspect,1.);

  float base = fbm2(uv, uTime*.05);
  base = base*.5-.65;

  float feed = base+(uDensity-.5)*.3;

  if(uEnableRipples==1){
    for(int i=0;i<MAX_CLICKS;++i){
      vec2 pos=uClickPos[i];
      if(pos.x<0.) continue;
      vec2 cuv=(((pos-uResolution*.5-cellPx*.5)/uResolution))*vec2(aspect,1.);
      float t  =max(uTime-uClickTimes[i],0.);
      float r  =distance(uv,cuv);
      float ring=exp(-pow((r-uRippleSpeed*t)/uRippleThickness,2.));
      float att =exp(-1.*t)*exp(-10.*r);
      feed=max(feed,ring*att*uRippleIntensity);
    }
  }

  float bayer   = Bayer8(fragCoord/uPixelSize)-.5;
  float bw      = step(.5,feed+bayer);

  float h       = fract(sin(dot(pixelId,vec2(127.1,311.7)))*43758.5453);
  float jitter  = 1.+(h-.5)*uPixelJitter;
  float coverage= bw*jitter;

  float M;
  if     (uShapeType==SHAPE_CIRCLE)   M=maskCircle  (pixelUV,coverage);
  else if(uShapeType==SHAPE_TRIANGLE) M=maskTriangle (pixelUV,pixelId,coverage);
  else if(uShapeType==SHAPE_DIAMOND)  M=maskDiamond  (pixelUV,coverage);
  else                                M=coverage;

  if(uEdgeFade>0.){
    vec2 norm=gl_FragCoord.xy/uResolution;
    float edge=min(min(norm.x,norm.y),min(1.-norm.x,1.-norm.y));
    M*=smoothstep(0.,uEdgeFade,edge);
  }

  /* sRGB gamma correction */
  vec3 srgb=mix(
    uColor*12.92,
    1.055*pow(uColor,vec3(1./2.4))-.055,
    step(0.0031308,uColor)
  );

  fragColor=vec4(srgb,M);
}`;

    /* ── Création du canvas fixe en arrière-plan ─────────────────── */
    var canvas = document.createElement('canvas');
    canvas.style.cssText = [
        'position:fixed','top:0','left:0',
        'width:100%','height:100%',
        'z-index:-1','pointer-events:none',
        'display:block'
    ].join(';');
    document.body.prepend(canvas);

    /* ── Renderer Three.js ───────────────────────────────────────── */
    var renderer = new THREE.WebGLRenderer({
        canvas: canvas,
        antialias: true,
        alpha: true,
        powerPreference: 'high-performance'
    });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.setClearAlpha(0);

    /* ── Uniforms ────────────────────────────────────────────────── */
    var clickPositions = [];
    var clickTimes     = new Float32Array(MAX_CLICKS);
    for (var i = 0; i < MAX_CLICKS; i++) {
        clickPositions.push(new THREE.Vector2(-1, -1));
    }

    var uniforms = {
        uResolution:    { value: new THREE.Vector2(1, 1) },
        uTime:          { value: 0 },
        uColor:         { value: new THREE.Color(CFG.color) },
        uClickPos:      { value: clickPositions },
        uClickTimes:    { value: clickTimes },
        uShapeType:     { value: 0 },   /* square */
        uPixelSize:     { value: CFG.pixelSize * renderer.getPixelRatio() },
        uScale:         { value: CFG.patternScale },
        uDensity:       { value: CFG.patternDensity },
        uPixelJitter:   { value: CFG.pixelSizeJitter },
        uEnableRipples: { value: CFG.enableRipples ? 1 : 0 },
        uRippleSpeed:   { value: CFG.rippleSpeed },
        uRippleThickness:{ value: CFG.rippleThickness },
        uRippleIntensity:{ value: CFG.rippleIntensityScale },
        uEdgeFade:      { value: CFG.edgeFade },
    };

    /* ── Scène ───────────────────────────────────────────────────── */
    var scene    = new THREE.Scene();
    var camera   = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);
    var material = new THREE.ShaderMaterial({
        vertexShader:   VERTEX_SRC,
        fragmentShader: FRAGMENT_SRC,
        uniforms:       uniforms,
        transparent:    true,
        depthTest:      false,
        depthWrite:     false,
        glslVersion:    THREE.GLSL3
    });
    scene.add(new THREE.Mesh(new THREE.PlaneGeometry(2, 2), material));

    /* ── Resize ──────────────────────────────────────────────────── */
    function onResize() {
        var w = window.innerWidth;
        var h = window.innerHeight;
        renderer.setSize(w, h, false);
        uniforms.uResolution.value.set(
            renderer.domElement.width,
            renderer.domElement.height
        );
        uniforms.uPixelSize.value = CFG.pixelSize * renderer.getPixelRatio();
    }
    onResize();
    window.addEventListener('resize', onResize, { passive: true });

    /* ── Ripples au clic ─────────────────────────────────────────── */
    var clickIx    = 0;
    var timeOffset = Math.random() * 1000;

    document.addEventListener('pointerdown', function (e) {
        var rect   = renderer.domElement.getBoundingClientRect();
        var scaleX = renderer.domElement.width  / rect.width;
        var scaleY = renderer.domElement.height / rect.height;
        var fx     = (e.clientX - rect.left)  * scaleX;
        var fy     = (rect.height - (e.clientY - rect.top)) * scaleY;
        uniforms.uClickPos.value[clickIx].set(fx, fy);
        uniforms.uClickTimes.value[clickIx] = uniforms.uTime.value;
        clickIx = (clickIx + 1) % MAX_CLICKS;
    }, { passive: true });

    /* ── Adapter la couleur selon le thème ──────────────────────── */
    function syncTheme() {
        var dark = document.documentElement.getAttribute('data-theme') === 'dark';
        /* En dark mode on garde le violet, en light on le rend plus transparent */
        uniforms.uColor.value.set(CFG.color);
        uniforms.uDensity.value = dark ? 1.6 : 1.2;
    }
    syncTheme();
    /* Observer les changements de thème */
    var themeObserver = new MutationObserver(syncTheme);
    themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });

    /* ── Boucle d'animation ──────────────────────────────────────── */
    var clock = new THREE.Clock();

    /* Pause quand l'onglet est invisible */
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) clock.start();
    });

    (function animate() {
        requestAnimationFrame(animate);
        if (document.hidden) return;
        uniforms.uTime.value = timeOffset + clock.getElapsedTime() * CFG.speed;
        renderer.render(scene, camera);
    })();

})();
