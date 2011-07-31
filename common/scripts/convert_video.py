#!/usr/bin/python

import subprocess, sys, os

video_in = sys.argv[1]
out_path = sys.argv[2]
video_id = sys.argv[3]

temp_fp = out_path + 'temp_' + video_id
final_fp = out_path + video_id

ogv_c = '/usr/bin/ffmpeg2theora -o ' + temp_fp + '.ogv ' + video_in
ogv_r = os.system(ogv_c)
os.rename(temp_fp + '.ogv', final_fp + '.ogv')

mp4_c = 'ffmpeg -i ' + video_in + ' -f mp4 -vcodec libx264 -an -vpre slow -b 1000k -bt 1000k -y '+ temp_fp + '.mp4'
mp4_r = os.system(mp4_c)
os.rename(temp_fp + '.mp4', final_fp + '.mp4')

os.remove(video_in)
