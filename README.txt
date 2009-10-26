
This extension is simply self-explanatory. Just go to the fileadmin module,
and click on the icon of an Image. Choose the option "Image Editor" from
the popup/context menu


Example USER/GROUP TS-Config :




mod.tx_kbimageedit_cm1 {
# Disable zoom completly:
#	disable_zoom = 1

# Clear all predefined zoom levels:
	clearZoomLevels = 1

# set zoomLevels (percent = label)
	zoomLevels {
		100 = 1:1
		133 = 133%
	}

# Disable the background color selection for rotate
	disable_rotateColors = 1

# rotate preset (angle = label)
	rotatePresets {
		30 = 30°
		180 = -
	}

	cropFrames {
		10 {
			label = 320x240
			width = 320
			height = 240
		}
		20 {
			label = 100x100
			width = 100
			height = 100
		}
		30 {
			label = 200x100
			width = 200
			height = 100
		}
		40 {
			label = 50%
			width = 50%
			aspect = 1
		}
	}
	scaleFrames {
		10 {
			label = 50%
			width = 50%
			aspect = 1
		}
	}


	disable_BASIC_TEXT = 1
	disable_FILE_SAVE = 1
	disable_EFFECT_BLUR = 1
	disable_EFFECT_SHARPEN = 1
	disable_EFFECT_GAMMA = 1
	disable_EFFECT_SOLARIZE = 1
	disable_EFFECT_SWIRL = 1
	disable_EFFECT_WAVE = 1
	disable_EFFECT_CHARCOAL = 1
	disable_EFFECT_GRAY = 1
	disable_EFFECT_EDGE = 1
	disable_EFFECT_EMBOSS = 1
#	disable_EFFECT_FLIP = 1
#	disable_EFFECT_FLOP = 1
	disable_EFFECT_COLORS = 1
	disable_EFFECT_SHEAR = 1
	disable_EFFECT_INVERT = 1
	disable_IMAGE_OVERLAY = 1
	disable_DRAW_RECTANGLE = 1
	disable_DRAW_CIRCLE = 1
}
