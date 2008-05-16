#!/bin/bash 
echo "Galileo Data Visualization Temp Directory Cleanup CRON Job"
echo "Time Check:" `date`
export VISUALIZ_INSTALL_DIR="/opt/visual/application/rati"
echo "Installation directory is set as.... $VISUALIZ_INSTALL_DIR" 
echo "Executing clean up...."
rm -fv $VISUALIZ_INSTALL_DIR/map/*
rm -fv $VISUALIZ_INSTALL_DIR/dot/*
rm -fv $VISUALIZ_INSTALL_DIR/img/*
echo "Cleanup is complete!"
echo "Time Check:" `date`
